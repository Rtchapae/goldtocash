
import { post, get } from './client'

export const queryCampaignStats = async (params = {}) => {
	return post('/admin/trace/campaign-stats/query', params)
}

export const getTraceFilterOptions = async () => {
	return get('/admin/trace/filter-options')
}

