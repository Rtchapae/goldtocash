import { get } from './client.js'

const formatPrice = (price) => {
	const numPrice = typeof price === 'string' ? parseFloat(price) : price

	if (isNaN(numPrice) || numPrice < 0) {
		return null
	}

	return new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(numPrice)
}

export const getCurrentGoldPrice = async () => {
	const data = await get('/calculator/current-price')
	const price = data?.data?.price

	if (price === null || price === undefined) {
		throw new Error('Price not found in response')
	}

	const numericPrice = typeof price === 'string' ? parseFloat(price) : price

	// Backend returns 0 when Redis `gold-price` is missing — treat as unavailable
	if (isNaN(numericPrice) || numericPrice <= 0) {
		throw new Error('Invalid price value')
	}

	const formatted = formatPrice(numericPrice)
	if (!formatted) {
		throw new Error('Failed to format price')
	}

	return {
		formatted,
		numeric: numericPrice
	}
}

