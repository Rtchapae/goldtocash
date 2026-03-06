
export const getDefaultDateRange = () => {
	const today = new Date()
	const sevenDaysAgo = new Date(today)
	sevenDaysAgo.setDate(today.getDate() - 7)
	
	const formatDate = (date) => {
		const year = date.getFullYear()
		const month = String(date.getMonth() + 1).padStart(2, '0')
		const day = String(date.getDate()).padStart(2, '0')
		return `${year}-${month}-${day}`
	}
	
	return {
		dateFrom: formatDate(sevenDaysAgo),
		dateTo: formatDate(today)
	}
}

export const buildQueryParams = (filters) => {
	const params = {}
	if (filters.eventType) params.eventType = filters.eventType
	if (filters.source) params.source = filters.source
	if (filters.campaign) params.campaign = filters.campaign
	if (filters.medium) params.medium = filters.medium
	if (filters.term) params.term = filters.term
	if (filters.content) params.content = filters.content
	if (filters.dateFrom) params.dateFrom = filters.dateFrom
	if (filters.dateTo) params.dateTo = filters.dateTo
	return params
}

