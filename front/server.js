import express from 'express'
import compression from 'compression'
import path from 'node:path'
import { readFileSync, existsSync } from 'node:fs'
import { fileURLToPath, pathToFileURL } from 'node:url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

function loadLegacyImageRedirects() {
	const p = path.join(__dirname, 'legacy-image-redirects.json')
	if (!existsSync(p)) return {}
	try {
		const o = JSON.parse(readFileSync(p, 'utf8'))
		return typeof o === 'object' && o !== null && !Array.isArray(o) ? o : {}
	} catch {
		return {}
	}
}

const isProd = process.env.NODE_ENV === 'production'
const port = process.env.PORT ? Number(process.env.PORT) : 5175

async function loadStaticSitemapPaths(vite) {
	if (!isProd && vite) {
		const routerModule = await vite.ssrLoadModule('/src/router/index.js')
		return routerModule.getStaticSitemapPaths()
	}

	const entryPath = path.resolve(__dirname, 'dist/server/entry-server.js')
	try {
		const entryServer = await import(pathToFileURL(entryPath).href)
		if (typeof entryServer.getStaticSitemapPaths === 'function') {
			return entryServer.getStaticSitemapPaths()
		}
	} catch (e) {
		console.warn('Sitemap: failed to load getStaticSitemapPaths from SSR bundle', e.message || e)
	}

	const fallbackPath = path.join(__dirname, 'public/static-sitemap-paths.json')
	if (existsSync(fallbackPath)) {
		try {
			const parsed = JSON.parse(readFileSync(fallbackPath, 'utf8'))
			if (Array.isArray(parsed)) {
				console.log('Sitemap: using static-sitemap-paths.json fallback (', parsed.length, 'paths)')
				return parsed
			}
		} catch (e) {
			console.warn('Sitemap: failed to read static-sitemap-paths.json', e.message || e)
		}
	}

	return ['/']
}

async function createServer() {
	const app = express()
	app.use(compression())

	// Old .png URLs after lossy migration (bookmarks, email, CDN) → current file
	const legacyImages = loadLegacyImageRedirects()
	for (const [fromPath, toPath] of Object.entries(legacyImages)) {
		if (typeof fromPath !== 'string' || typeof toPath !== 'string') continue
		app.get(fromPath, (_req, res) => {
			res.redirect(301, toPath)
		})
	}

	// Log every request first (so we see in logs if nginx reaches this process)
	app.use((req, res, next) => {
		console.log('[Front]', req.method, req.url)
		next()
	})

	let vite
	if (!isProd) {
		// Development: Vite first so it handles /src, CSS, transforms before static
		const { createServer } = await import('vite')
		vite = await createServer({
			root: __dirname,
			logLevel: 'info',
			server: {
				middlewareMode: true
			},
			appType: 'custom'
		})
		app.use(vite.middlewares)
		console.log('Running in development SSR mode (HMR disabled)')
	}

	if (isProd) {
		app.use('/assets', express.static(path.join(__dirname, 'dist/client/assets')))
	}
	app.use(express.static(path.join(__dirname, 'public'), { index: false }))
	app.use('/images', express.static(path.join(__dirname, 'public/images')))
	app.use('/img', express.static(path.join(__dirname, 'src/assets/img')))

	// Sitemap: static paths from front router, dynamic slugs from backend (fallback to static-only if backend fails)
	app.get('/sitemap.xml', async (req, res) => {
		const requestHost = req.get('host') || 'localhost'
		const siteBaseUrl = process.env.SITE_URL || `${req.protocol}://${requestHost}`
		const base = siteBaseUrl.replace(/\/$/, '')

		// API URL: only API_BASE_URL override, else same-origin (host that requested sitemap). Do not use VITE_API_BASE_URL here — it may point to another domain (e.g. goldtocash.us) while sitemap is on base.goldtocash.us.
		const apiBaseUrl = process.env.API_BASE_URL || `${req.protocol}://${requestHost}/api/v1`
		const apiHost = process.env.API_HOST || (process.env.SITE_URL ? new URL(process.env.SITE_URL).hostname : null)

		let staticPaths
		try {
			staticPaths = await loadStaticSitemapPaths(vite)
		} catch (e) {
			console.error('Sitemap: failed to load static paths', e)
			res.status(500).set('Content-Type', 'text/plain').end('Sitemap error')
			return
		}

		let data = { gold_info: [], sell: [], sell_gold: [] }
		const sitemapUrlsPath = `${apiBaseUrl.replace(/\/$/, '')}/sitemap-urls`
		const fetchHeaders = { Accept: 'application/json' }
		if (apiHost) fetchHeaders.Host = apiHost
		try {
			const response = await fetch(sitemapUrlsPath, {
				headers: fetchHeaders
			})
			if (response.ok) {
				data = await response.json()
				const total = (data.gold_info?.length || 0) + (data.sell?.length || 0) + (data.sell_gold?.length || 0)
				if (total > 0) {
					console.log('Sitemap: loaded', total, 'post slugs from backend')
				}
			} else {
				console.warn('Sitemap: backend returned', response.status, 'from', sitemapUrlsPath, '- using static paths only')
			}
		} catch (e) {
			console.warn('Sitemap: backend fetch failed', e.message || e, 'URL:', sitemapUrlsPath, '- using static paths only')
		}

		const escapeXml = (s) => String(s)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&apos;')

		const loc = (path) => escapeXml(path === '/' ? base : `${base}${path.startsWith('/') ? path : '/' + path}`)

		const urls = []
		for (const p of staticPaths) {
			urls.push(`  <url><loc>${loc(p)}</loc><changefreq>weekly</changefreq><priority>${p === '/' ? '1.0' : '0.8'}</priority></url>`)
		}
		for (const slug of data.gold_info || []) {
			urls.push(`  <url><loc>${loc(`/gold-info/${slug}`)}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>`)
		}
		for (const slug of data.sell || []) {
			urls.push(`  <url><loc>${loc(`/sell/${slug}`)}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>`)
		}
		for (const slug of data.sell_gold || []) {
			urls.push(`  <url><loc>${loc(`/sell-gold/${slug}`)}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>`)
		}

		const hasPosts = (data.gold_info?.length || 0) + (data.sell?.length || 0) + (data.sell_gold?.length || 0) > 0
		const comment = hasPosts ? '' : '<!-- Sitemap: no post slugs from backend (check API_BASE_URL and backend logs) -->\n'

		const xml = `<?xml version="1.0" encoding="UTF-8"?>
${comment}<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urls.join('\n')}
</urlset>`

		res.status(200)
			.set('Content-Type', 'application/xml')
			.set('Cache-Control', 'public, max-age=3600')
			.set('X-Sitemap-Posts', hasPosts ? 'yes' : 'no')
			.end(xml)
	})

	app.use('*', async (req, res, next) => {
		// Skip static assets - they should be handled by static middleware
		if (req.url.startsWith('/assets/') || req.url.startsWith('/images/') || req.url.startsWith('/img/')) {
			return next()
		}

		try {
			console.time('Server Request Time')
			const url = req.originalUrl
			console.timeLog('Server Request Time', `Request for: ${url}`)

			let template
			let render

			if (!isProd) {
				// Development: use Vite SSR
				const fs = await import('node:fs')
				template = fs.readFileSync(path.resolve(__dirname, 'index.html'), 'utf-8')
				template = await vite.transformIndexHtml(url, template)
				render = (await vite.ssrLoadModule('/src/entry-server.js')).render
			} else {
				// Production: use pre-built SSR files
				const fs = await import('node:fs')
				const templatePath = path.resolve(__dirname, 'dist/client/index.html')
				if (!fs.existsSync(templatePath)) {
					console.error('[Front] Missing template:', templatePath)
					return res.status(500).set('Content-Type', 'text/plain').end('Server config error: missing dist. Rebuild the front app.')
				}
				template = fs.readFileSync(templatePath, 'utf-8')
				if (!template.includes('<!--app-html-->') || !template.includes('<!--pinia-state-->')) {
					console.error('[Front] Template missing SSR placeholders (<!--app-html--> or <!--pinia-state-->). Check index.html and rebuild.')
					return res.status(500).set('Content-Type', 'text/plain').end('Server config error: invalid template. Rebuild the front app.')
				}
				// No Vite transform in production
				render = (await import(path.resolve(__dirname, 'dist/server/entry-server.js'))).render
			}

			console.timeLog('Server Request Time', 'Template loaded')

			const { appHtml, state, metaTags, statusCode } = await render(url, {
				ssrApiBaseUrl: process.env.SSR_API_BASE_URL || undefined,
			})
			console.timeLog('Server Request Time', 'SSR render completed')

			let html = template
				.replace('<!--app-html-->', appHtml)
				.replace('<!--pinia-state-->', `<script>window.__PINIA__=${JSON.stringify(state)}</script>`)

			// Inject SEO meta tags if available
			if (metaTags) {
				html = html.replace('<title>Gold to Cash</title>', metaTags.trim())
			}

			console.timeEnd('Server Request Time')
			res.status(statusCode || 200).set({ 'Content-Type': 'text/html' }).end(html)
		} catch (e) {
			if (!isProd && vite) vite.ssrFixStacktrace(e)
			console.error('SSR Error:', e)
			next(e)
		}
	})

	// Error handler: return 500 so nginx gets a valid response instead of 502
	app.use((err, req, res, next) => {
		console.error('Unhandled error:', err)
		res.status(500).set('Content-Type', 'text/html').end(
			'<!DOCTYPE html><html><head><title>Error</title></head><body><h1>Server Error</h1><p>Please try again later.</p></body></html>'
		)
	})

	console.log('[Front] Starting listen on port', port)
	try {
		app.listen(port, '0.0.0.0', () => {
			console.log(`SSR server running at http://0.0.0.0:${port}`)
		})
	} catch (err) {
		console.error('[Front] listen failed:', err)
		throw err
	}
}

createServer().catch((err) => {
	console.error('[Front] Fatal startup error:', err)
	process.exit(1)
})


