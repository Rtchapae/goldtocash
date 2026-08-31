/**
 * Absolute URL for files returned by Laravel as "/storage/..." while the admin SPA
 * runs on another origin (e.g. Vite :5173 with API on :8888).
 */
export function resolveAdminAssetUrl(url) {
	if (!url || typeof url !== 'string') {
		return null
	}
	const u = url.trim()
	if (!u) {
		return null
	}
	if (/^https?:\/\//i.test(u)) {
		return u
	}
	if (u.startsWith('//')) {
		if (typeof window === 'undefined') {
			return u
		}
		return `${window.location.protocol}${u}`
	}
	if (u.startsWith('/')) {
		const raw = import.meta.env.VITE_ADMIN_API_URL || import.meta.env.VITE_API_BASE_URL || ''
		if (/^https?:\/\//i.test(raw)) {
			try {
				return `${new URL(raw).origin}${u}`
			} catch {
				// fall through
			}
		}
		if (typeof window !== 'undefined') {
			return `${window.location.origin}${u}`
		}
	}
	return u
}
