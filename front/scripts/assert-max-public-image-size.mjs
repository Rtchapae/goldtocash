#!/usr/bin/env node
/**
 * Fails the process if any raster under front/public exceeds MAX_BYTES (default 1 MiB).
 * No dependencies — use after compress or in CI as a gate.
 *
 *   MAX_BYTES=1048576 node scripts/assert-max-public-image-size.mjs
 */
import fs from 'node:fs/promises'
import path from 'node:path'

const MAX_BYTES = Number(process.env.MAX_BYTES || 1024 * 1024)
const PUBLIC_DIR = path.resolve(process.cwd(), 'public')
const exts = new Set(['.jpg', '.jpeg', '.png', '.webp', '.gif'])

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

async function main() {
	await fs.access(PUBLIC_DIR)
	const bad = []
	for await (const file of walkImages(PUBLIC_DIR)) {
		const st = await fs.stat(file)
		if (st.size > MAX_BYTES) {
			bad.push({
				rel: path.relative(PUBLIC_DIR, file),
				mb: (st.size / (1024 * 1024)).toFixed(2),
			})
		}
	}
	if (bad.length) {
		console.error(
			`[images] ${bad.length} file(s) exceed ${(MAX_BYTES / (1024 * 1024)).toFixed(1)} MiB under public/:`,
		)
		for (const b of bad) {
			console.error(`  — ${b.rel} (${b.mb} MiB)`)
		}
		console.error('Run: yarn images:compress   (from front/)')
		process.exit(1)
	}
	console.log(
		`[images] OK — no public raster larger than ${(MAX_BYTES / (1024 * 1024)).toFixed(1)} MiB.`,
	)
}

main().catch((e) => {
	console.error(e)
	process.exit(1)
})
