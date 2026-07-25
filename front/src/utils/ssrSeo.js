import { BLOG_POST_ROUTE_NAMES, blogPathPrefixForRouteName } from '@/constants/blogPostRoutes.js'

const DEFAULT_META = {
	title: 'Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer',
	description:
		'Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.',
	keywords: 'gold, silver, precious metals, gold buyer, silver buyer, appraisal, kit request',
}

const NOINDEX_ROUTE_NAMES = new Set([
	'sign-in',
	'forgot-password',
	'not-found',
	'user-account',
	'kit-request-success',
])

function resolveSiteBaseUrl() {
	const strip = (u) => String(u).replace(/\/$/, '')
	if (typeof process !== 'undefined' && process.env.SITE_URL) {
		return strip(process.env.SITE_URL)
	}
	return 'https://goldtocash.us'
}

function canonicalPathFromRoute(route) {
	const path = route?.path || '/'
	if (path === '/') return '/'
	return path.startsWith('/') ? path : `/${path}`
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

function excerptFromHtml(html) {
	const text = String(html ?? '')
		.replace(/<[^>]+>/g, ' ')
		.replace(/\s+/g, ' ')
		.trim()
	if (!text) return ''
	return text.length > 160 ? `${text.slice(0, 157)}...` : text
}

/**
 * @param {string} apiBase
 * @param {string} slug
 * @param {string | null} pathPrefix
 * @param {{ title: string, description: string, keywords: string }} defaults
 * @returns {Promise<{ title: string, description: string, keywords: string } | null>}
 */
async function fetchPostMetaForSsr(apiBase, slug, pathPrefix, defaults) {
	const qs = new URLSearchParams()
	if (pathPrefix != null && pathPrefix !== '') {
		qs.append('path_prefix', pathPrefix)
	}
	const q = qs.toString()
	const url = `${apiBase}/posts/${encodeURIComponent(slug)}${q ? `?${q}` : ''}`
	try {
		const controller = new AbortController()
		const t = setTimeout(() => controller.abort(), 10000)
		const res = await fetch(url, {
			headers: { Accept: 'application/json' },
			signal: controller.signal,
		})
		clearTimeout(t)
		if (!res.ok) return null
		const json = await res.json()
		const post = json?.data
		if (!post || typeof post !== 'object') return null

		const title =
			(post.seo_title && String(post.seo_title).trim()) || post.title || defaults.title
		const description =
			(post.seo_description && String(post.seo_description).trim()) ||
			excerptFromHtml(post.body) ||
			defaults.description

		return {
			title,
			description,
			keywords: defaults.keywords,
		}
	} catch {
		return null
	}
}

/**
 * Fetch SEO from the same API as the client (admin-configured pages).
 * @param {{ name?: string, path?: string, params?: Record<string, string> }} route
 * @param {{ apiBaseUrl?: string }} [options] — absolute API root for server-side fetch (required in Docker if VITE_API_BASE_URL is relative)
 * @returns {Promise<string>} HTML fragment for <head> (title + meta tags)
 */
export async function buildSsrMetaTags(route, options = {}) {
	const defaults = { ...DEFAULT_META }
	const siteBase = resolveSiteBaseUrl()
	const canonicalPath = canonicalPathFromRoute(route)
	const canonicalUrl = `${siteBase}${canonicalPath === '/' ? '' : canonicalPath}`
	const noindex = NOINDEX_ROUTE_NAMES.has(route?.name != null ? String(route.name) : '')
	const apiBase = resolveApiBaseUrl(options.apiBaseUrl)
	if (!apiBase) {
		return metaTagsHtml(defaults, { canonicalUrl, noindex })
	}

	const routeName = route?.name != null ? String(route.name) : ''
	const slug = route?.params?.slug
	if (slug && BLOG_POST_ROUTE_NAMES.includes(routeName)) {
		const pathPrefix = blogPathPrefixForRouteName(routeName)
		const postMeta = await fetchPostMetaForSsr(apiBase, slug, pathPrefix, defaults)
		if (postMeta) {
			return metaTagsHtml(postMeta, { canonicalUrl, noindex })
		}
	}

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
		return metaTagsHtml(defaults, { canonicalUrl, noindex })
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

	return metaTagsHtml({ title, description, keywords }, { canonicalUrl, noindex })
}

function metaTagsHtml({ title, description, keywords }, { canonicalUrl, noindex = false } = {}) {
	const t = escapeMetaText(title)
	const d = escapeMetaText(description)
	const k = escapeMetaText(keywords)
	const c = escapeMetaText(canonicalUrl)
	const robots = noindex ? 'noindex, nofollow' : 'index, follow'
	return `
	<title>${t}</title>
	<meta name="description" content="${d}">
	<meta name="keywords" content="${k}">
	<meta name="robots" content="${robots}">
	<link rel="canonical" href="${c}">
	<meta property="og:title" content="${t}">
	<meta property="og:description" content="${d}">
	<meta property="og:type" content="website">
	<meta property="og:url" content="${c}">
`
}
