import { post, get, downloadFile } from './client.js'

const ALLOWED = ['first_name', 'last_name', 'email', 'phone', 'address', 'address2', 'city', 'state', 'zip', 'country', 'verification_code', 'allow_unverified']

function s(v) { return v != null ? String(v).trim() : '' }

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
	const body = buildKitPayload(data)
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
