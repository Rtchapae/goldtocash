import { get } from './client.js'

export const getSeoData = async (routeName, urlPath = '/') => {
  try {
    const response = await get(`seo?page_url=${encodeURIComponent(urlPath)}&route_name=${encodeURIComponent(routeName)}`)
    return response.data || null
  } catch (error) {
    console.warn('Failed to load SEO data:', error)
    return null
  }
}

export const getCurrentRouteSeoData = async (route) => {
  const routeName = route?.name || null
  const urlPath = route?.path || '/'
  return await getSeoData(routeName, urlPath)
}
