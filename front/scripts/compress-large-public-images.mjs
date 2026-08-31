#!/usr/bin/env node
/**
 * Shrinks raster images under front/public that exceed MAX_BYTES (default 1 MiB).
 * Optional — not part of Docker/production build. Requires sharp once:
 *   cd front && yarn add -D sharp && yarn images:compress
 *
 * PNG with transparency: resize + PNG compression only.
 * PNG without alpha: JPEG if still too large after shrinking.
 */
import fs from 'node:fs/promises'
import path from 'node:path'

let sharp
try {
	;({ default: sharp } = await import('sharp'))
} catch {
	console.error('[images] Install sharp to run this script: yarn add -D sharp')
	process.exit(1)
}

const MAX_BYTES = Number(process.env.MAX_BYTES || 1024 * 1024)
const PUBLIC_DIR = path.resolve(process.cwd(), 'public')
const LEGACY_JSON = path.resolve(process.cwd(), 'legacy-image-redirects.json')
const MIN_WIDTH = 480

async function recordLegacyRedirect(fromUrl, toUrl) {
	let map = {}
	try {
		const raw = await fs.readFile(LEGACY_JSON, 'utf8')
		map = JSON.parse(raw)
	} catch {
		// missing or invalid — start fresh
	}
	if (typeof map !== 'object' || map === null || Array.isArray(map)) map = {}
	map[fromUrl] = toUrl
	await fs.writeFile(LEGACY_JSON, `${JSON.stringify(map, null, '\t')}\n`)
}

const exts = new Set(['.jpg', '.jpeg', '.png', '.webp'])

async function* walkImages(dir) {
	const entries = await fs.readdir(dir, { withFileTypes: true })
	for (const e of entries) {
		const full = path.join(dir, e.name)
		if (e.isDirectory()) {
			if (e.name === 'node_modules') continue
			yield* walkImages(full)
		} else if (e.isFile() && exts.has(path.extname(e.name).toLowerCase())) {
			yield full
		}
	}
}

async function compressOne(absPath) {
	const before = (await fs.stat(absPath)).size
	if (before <= MAX_BYTES) return null

	const input = await fs.readFile(absPath)
	const meta = await sharp(input).metadata()
	const ext = path.extname(absPath).toLowerCase()
	const hasAlpha = Boolean(meta.hasAlpha)
	const origW = meta.width || 2000
	let width = Math.min(origW, 2560)

	const resizeIfNeeded = (w) => {
		let p = sharp(input).rotate()
		if (w < origW) {
			p = p.resize({ width: w, fit: 'inside', withoutEnlargement: true })
		}
		return p
	}

	while (width >= MIN_WIDTH) {
		let buf

		if (ext === '.png') {
			buf = await resizeIfNeeded(width).png({ compressionLevel: 9, effort: 10 }).toBuffer()
			if (buf.length > MAX_BYTES && !hasAlpha) {
				for (let q = 82; q >= 55; q -= 4) {
					buf = await resizeIfNeeded(width).jpeg({ quality: q, mozjpeg: true }).toBuffer()
					if (buf.length <= MAX_BYTES) break
				}
			}
		} else if (ext === '.webp') {
			for (let q = 86; q >= 50; q -= 5) {
				buf = await resizeIfNeeded(width).webp({ quality: q }).toBuffer()
				if (buf.length <= MAX_BYTES) break
			}
		} else {
			for (let q = 88; q >= 50; q -= 4) {
				buf = await resizeIfNeeded(width).jpeg({ quality: q, mozjpeg: true }).toBuffer()
				if (buf.length <= MAX_BYTES) break
			}
		}

		if (buf && buf.length <= MAX_BYTES) {
			const isJpegOutput = ext === '.png' && !hasAlpha && (await sharp(buf).metadata()).format === 'jpeg'
			if (isJpegOutput) {
				const jpgPath = absPath.replace(/\.png$/i, '.jpg')
				await fs.unlink(absPath)
				await fs.writeFile(jpgPath, buf)
				const fromUrl = `/${path.relative(PUBLIC_DIR, absPath).replace(/\\/g, '/')}`
				const toUrl = `/${path.relative(PUBLIC_DIR, jpgPath).replace(/\\/g, '/')}`
				await recordLegacyRedirect(fromUrl, toUrl)
				return {
					file: path.relative(PUBLIC_DIR, absPath),
					before,
					after: buf.length,
					convertedToJpg: path.relative(PUBLIC_DIR, jpgPath),
				}
			}
			await fs.writeFile(absPath, buf)
			return { file: path.relative(PUBLIC_DIR, absPath), before, after: buf.length }
		}

		width = Math.floor(width * 0.87)
	}

	console.warn(`[images] Still over limit after shrinking: ${absPath}`)
	return null
}

async function main() {
	await fs.access(PUBLIC_DIR)
	let n = 0
	let failed = false
	for await (const file of walkImages(PUBLIC_DIR)) {
		const st = await fs.stat(file)
		if (st.size <= MAX_BYTES) continue
		const rel = path.relative(PUBLIC_DIR, file)
		process.stdout.write(`Compressing ${rel} (${(st.size / MAX_BYTES).toFixed(2)}× limit)… `)
		const r = await compressOne(file)
		if (r) {
			n++
			if (r.convertedToJpg) {
				console.log(`OK → ${r.convertedToJpg} (${(r.after / 1024).toFixed(0)} KiB)`)
			} else {
				console.log(`OK (${(r.after / 1024).toFixed(0)} KiB)`)
			}
		} else {
			console.log('failed')
			failed = true
		}
	}
	if (!n && !failed) {
		console.log(`No files over ${(MAX_BYTES / 1024).toFixed(0)} KiB under public/.`)
	}
	if (failed) {
		console.error('[images] Fix oversized assets manually or lower MIN_WIDTH in compress script.')
		process.exit(1)
	}
}

main().catch((e) => {
	console.error(e)
	process.exit(1)
})
