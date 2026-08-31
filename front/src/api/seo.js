import { get } from './client.js'

export const getSeoData = async (routeName, urlPath = '/') => {
  try {
    // Laravel отдаёт плоский JSON { title, description, keywords }, не { data: ... }
    const body = await get(
      `seo?page_url=${encodeURIComponent(urlPath)}&route_name=${encodeURIComponent(routeName ?? '')}`
    )
    if (body && typeof body === 'object' && (body.title != null || body.description != null)) {
      return body
    }
    return null
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
