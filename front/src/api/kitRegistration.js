import { post, get, downloadFile } from './client.js'

const ALLOWED = ['first_name', 'last_name', 'email', 'phone', 'address', 'address2', 'city', 'state', 'zip', 'country', 'verification_code', 'allow_unverified']

const ATTRIBUTION_KEYS = ['submission_url', 'referrer', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']

function s(v) { return v != null ? String(v).trim() : '' }

/**
 * Landing URL, referrer, and UTM params for kit registration (admin orders URL / source).
 */
export function collectKitAttribution() {
	if (typeof window === 'undefined' || typeof document === 'undefined') {
		return {}
	}
	const out = {}
	try {
		const href = window.location.href || ''
		if (href) {
			out.submission_url = href.length > 2048 ? href.slice(0, 2048) : href
		}
		const ref = document.referrer || ''
		if (ref) {
			out.referrer = ref.length > 2048 ? ref.slice(0, 2048) : ref
		}
		const params = new URLSearchParams(window.location.search || '')
		for (const k of ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']) {
			const v = params.get(k)
			if (v && s(v)) {
				const t = s(v)
				out[k] = t.length > 255 ? t.slice(0, 255) : t
			}
		}
	} catch (_) {
		// ignore
	}
	return out
}

/** Payload with ONLY these keys. street/streetNumber/fullAddress never sent. */
export function buildKitPayload(data) {
	const r = data || {}
	const addr = s(r.address) || s(r.street) || s(r.fullAddress)
	const out = {}
	for (const k of ALLOWED) {
		const v = k === 'address' ? addr : r[k]
		if (v != null) out[k] = s(v)
	}
	if (!out.address) out.address = addr
	return out
}

const DEBUG_KIT = true
const kitLog = (...args) => DEBUG_KIT && console.log('[kit/register]', ...args)

export const registerKit = async (data) => {
	kitLog('raw input keys:', Object.keys(data || {}))
	const body = { ...buildKitPayload(data), ...collectKitAttribution() }
	for (const k of ATTRIBUTION_KEYS) {
		const v = data?.[k]
		if (v != null && s(v) !== '') {
			const t = s(v)
			if (k === 'submission_url' || k === 'referrer') {
				body[k] = t.length > 2048 ? t.slice(0, 2048) : t
			} else {
				body[k] = t.length > 255 ? t.slice(0, 255) : t
			}
		}
	}
	kitLog('payload sent:', JSON.stringify(body))
	try {
		return await post('/kit/register', body, {}, true)
	} catch (err) {
		kitLog('API error response:', err?.status, err?.data)
		throw err
	}
}

export const getPrintLabel = async (orderId) => {
	return downloadFile(`/orders/${orderId}/print-label`)
}

export async function confirmAccountCreation(orderId) {
	return post(`/orders/${orderId}/confirm-account`)
}
