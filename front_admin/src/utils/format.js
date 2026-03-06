
export const formatName = (user) => {
	if (!user) return ''
	if (user.first_name || user.last_name) {
		return `${user.first_name || ''} ${user.last_name || ''}`.trim()
	}
	return user.name || ''
}
export const formatPhone = (phone) => {
	if (!phone) return ''
	const cleaned = phone.replace(/\D/g, '')
	if (cleaned.length === 10) {
		return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`
	}
	return phone
}

export const normalizePhone = (phone) => {
	if (!phone) return ''
	return phone.replace(/\D/g, '')
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


