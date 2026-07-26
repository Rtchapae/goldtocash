import { get } from './client.js'

export const fetchBuilderPage = async (slug) => {
	return get(`/builder-pages/${encodeURIComponent(slug)}`)
}
