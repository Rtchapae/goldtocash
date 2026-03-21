const DEFAULT_META = {
	title: 'Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer',
	description:
		'Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.',
	keywords: 'gold, silver, precious metals, gold buyer, silver buyer, appraisal, kit request',
}

/**
 * Node SSR needs an absolute API URL. Docker often sets VITE_API_BASE_URL=https://... via env_file;
 * build may bake /api/v1 — then set SSR_API_BASE_URL=http://nginx/api/v1 (internal) or full public URL.
 */
function resolveApiBaseUrl(explicitBase) {
	const strip = (u) => String(u).replace(/\/$/, '')
	if (explicitBase && String(explicitBase).trim()) {
		return strip(explicitBase)
	}
	if (typeof process !== 'undefined') {
		for (const key of ['SSR_API_BASE_URL', 'VITE_API_BASE_URL']) {
			const v = process.env[key]
			if (v && /^https?:\/\//i.test(v)) {
				return strip(v)
			}
		}
	}
	const vite = typeof import.meta !== 'undefined' ? import.meta.env?.VITE_API_BASE_URL : ''
	if (vite && /^https?:\/\//i.test(String(vite))) {
		return strip(vite)
	}
	return ''
}

function escapeMetaText(value) {
	return String(value ?? '')
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/"/g, '&quot;')
		.replace(/\s+/g, ' ')
		.trim()
}

/**
 * Fetch SEO from the same API as the client (admin-configured pages).
 * @param {{ name?: string, path?: string }} route
 * @param {{ apiBaseUrl?: string }} [options] — absolute API root for server-side fetch (required in Docker if VITE_API_BASE_URL is relative)
 * @returns {Promise<string>} HTML fragment for <head> (title + meta tags)
 */
export async function buildSsrMetaTags(route, options = {}) {
	const defaults = { ...DEFAULT_META }
	const apiBase = resolveApiBaseUrl(options.apiBaseUrl)
	if (!apiBase) {
		return metaTagsHtml(defaults)
	}

	const routeName = route?.name != null ? String(route.name) : ''
	const urlPath = route?.path || '/'
	const qs = new URLSearchParams({
		page_url: urlPath,
		route_name: routeName,
	})
	const url = `${apiBase}/seo?${qs.toString()}`

	let data = null
	try {
		const controller = new AbortController()
		const t = setTimeout(() => controller.abort(), 10000)
		const res = await fetch(url, {
			headers: { Accept: 'application/json' },
			signal: controller.signal,
		})
		clearTimeout(t)
		if (res.ok) {
			data = await res.json()
		}
	} catch {
		// API unreachable — fall back to defaults
	}

	if (!data || typeof data !== 'object') {
		return metaTagsHtml(defaults)
	}

	const title = (data.title && String(data.title).trim()) || defaults.title
	const description = (data.description && String(data.description).trim()) || defaults.description
	let keywords = defaults.keywords
	if (data.keywords) {
		keywords = Array.isArray(data.keywords)
			? data.keywords.filter(Boolean).join(', ')
			: String(data.keywords)
		if (!keywords.trim()) keywords = defaults.keywords
	}

	return metaTagsHtml({ title, description, keywords })
}

function metaTagsHtml({ title, description, keywords }) {
	const t = escapeMetaText(title)
	const d = escapeMetaText(description)
	const k = escapeMetaText(keywords)
	return `
	<title>${t}</title>
	<meta name="description" content="${d}">
	<meta name="keywords" content="${k}">
	<meta property="og:title" content="${t}">
	<meta property="og:description" content="${d}">
	<meta property="og:type" content="website">
`
}
