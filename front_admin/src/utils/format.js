
export const formatName = (user) => {
	if (!user) return ''
	if (user.first_name || user.last_name) {
		return `${user.first_name || ''} ${user.last_name || ''}`.trim()
	}
	return user.name || ''
}
/** US digits only (10), strips leading country code 1 from 11-digit numbers. */
export const phoneDigitsOnly = (phone) => {
	if (phone == null || phone === '') return ''
	let digits = String(phone).replace(/\D/g, '')
	if (digits.length === 11 && digits.startsWith('1')) {
		digits = digits.slice(1)
	}
	return digits
}

export const formatPhone = (phone) => {
	if (!phone) return ''
	const cleaned = phoneDigitsOnly(phone)
	if (cleaned.length === 10) {
		return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`
	}
	return String(phone).trim()
}

/** Digits for tel: links (US 10-digit, no +1 prefix). */
export const normalizePhone = (phone) => phoneDigitsOnly(phone)

const APT_IN_LINE1_PATTERN = /apt\.?|suite|unit|#|floor|fl\.?|ste\.?|bldg\.?|room|rm\.?|no\.?/i

/** Street line with optional apt/unit (address line 2). */
export const formatStreetAddress = (address, address2) => {
	const split = splitStreetAddress(address, address2)
	if (!split.address) return split.address2
	if (!split.address2) return split.address
	return `${split.address}, ${split.address2}`
}

/**
 * Normalize street + apt for forms when address2 was stored inside address (legacy / Places).
 */
export const splitStreetAddress = (address, address2) => {
	const line2 = String(address2 ?? '').trim()
	if (line2) {
		return {
			address: String(address ?? '').trim(),
			address2: line2,
		}
	}

	const line1 = String(address ?? '').trim()
	if (!line1) {
		return { address: '', address2: '' }
	}

	const parts = line1.split(',').map((part) => part.trim()).filter(Boolean)
	if (parts.length >= 2) {
		const maybeApt = parts[1]
		const looksLikeApt =
			APT_IN_LINE1_PATTERN.test(maybeApt)
			|| (maybeApt.length <= 20 && /\d/.test(maybeApt))

		if (looksLikeApt) {
			return {
				address: parts[0],
				address2: parts.slice(1).join(', '),
			}
		}
	}

	return { address: line1, address2: '' }
}

export const formatAmount = (amount) => {
	if (!amount || amount === 'None' || amount === 'none' || amount === null || amount === undefined) {
		return '$ 0.00'
	}
	
	if (typeof amount === 'string' && amount.startsWith('$')) {
		return amount
	}
	
	const numAmount = parseFloat(amount)
	if (isNaN(numAmount)) {
		return '$ 0.00'
	}
	
	return `$ ${numAmount.toFixed(2)}`
}


