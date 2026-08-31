export const BLOG_POST_ROUTE_NAMES = Object.freeze([
	'gold-info-post',
	'sell-article',
	'sell-gold-post',
])

export function blogPathPrefixForRouteName(routeName) {
	if (routeName === 'sell-gold-post') return '/sell-gold'
	if (routeName === 'sell-article') return '/sell'
	return null
}
