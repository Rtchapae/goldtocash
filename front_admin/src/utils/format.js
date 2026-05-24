
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


