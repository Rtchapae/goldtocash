import { get } from './client.js'

export const fetchLandingPageByPath = async (path) => {
	const query = new URLSearchParams({ path })
	return await get(`/landing-pages/by-path?${query.toString()}`, {}, true)
}

export const fetchLandingPagePaths = async () => {
	return await get('/landing-pages/paths', {}, true)
}
