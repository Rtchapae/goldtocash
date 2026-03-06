import { get } from './client.js'

export const fetchPosts = async (params = {}) => {
	const queryParams = new URLSearchParams()
	
	Object.keys(params).forEach(key => {
		if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
			queryParams.append(key, params[key])
		}
	})

	const queryString = queryParams.toString()
	const endpoint = `/posts${queryString ? `?${queryString}` : ''}`
	
	return await get(endpoint, {}, true)
}

export const fetchPost = async (slug, pathPrefix = null) => {
	const queryParams = new URLSearchParams()
	if (pathPrefix !== null) {
		queryParams.append('path_prefix', pathPrefix)
	}

	const queryString = queryParams.toString()
	const endpoint = `/posts/${slug}${queryString ? `?${queryString}` : ''}`
	
	return await get(endpoint, {}, true)
}

