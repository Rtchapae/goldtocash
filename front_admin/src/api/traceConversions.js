
import { get } from './client'

export const queryTraceConversions = async (params = {}) => {
	const queryParams = new URLSearchParams()
	
	if (params.source) queryParams.append('source', params.source)
	if (params.campaign) queryParams.append('campaign', params.campaign)
	if (params.medium) queryParams.append('medium', params.medium)
	if (params.term) queryParams.append('term', params.term)
	if (params.content) queryParams.append('content', params.content)
	if (params.dateFrom) queryParams.append('dateFrom', params.dateFrom)
	if (params.dateTo) queryParams.append('dateTo', params.dateTo)
	
	const endpoint = `/admin/trace/conversions/query${queryParams.toString() ? '?' + queryParams.toString() : ''}`
	return get(endpoint)
}

