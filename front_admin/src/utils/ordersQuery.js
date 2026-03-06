
export const buildOrdersQueryParams = (params = {}, options = {}) => {
	const {
		includeOrderType = false,
		includeBranchId = false
	} = options

	const queryParams = new URLSearchParams()

	const perPageValue = params.per_page !== undefined ? params.per_page : params.perPage
	if (perPageValue !== undefined && perPageValue !== null) {
		queryParams.set('per_page', String(perPageValue))
	}

	if (params.page !== undefined && params.page !== null) {
		queryParams.set('page', String(params.page))
	}
	if (params.period) {
		queryParams.set('period', params.period)
	}

	if (params.nameQuery) queryParams.set('nameQuery', params.nameQuery)
	if (params.source && params.source !== 'any') queryParams.set('source', params.source)
	if (params.utm_campaign && params.utm_campaign !== 'any') queryParams.set('utm_campaign', params.utm_campaign)
	if (params.utm_medium && params.utm_medium !== 'any') queryParams.set('utm_medium', params.utm_medium)
	if (params.from) queryParams.set('from', params.from)
	if (params.to) queryParams.set('to', params.to)
	if (params['order-by']) queryParams.set('order-by', params['order-by'])
	if (params['order-dir']) queryParams.set('order-dir', params['order-dir'])

	if (includeOrderType && params.order_type) queryParams.set('order_type', params.order_type)
	if (includeBranchId && params.branch_id) queryParams.set('branch_id', params.branch_id)

	return queryParams
}

