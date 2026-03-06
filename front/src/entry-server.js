import { createApp } from './app'
import { createMemoryHistory } from 'vue-router'
import { renderToString } from '@vue/server-renderer'
import { getStaticSitemapPaths } from './router'

export { getStaticSitemapPaths }

export async function render(url) {
	const { app, router, pinia } = createApp(createMemoryHistory())

	await router.push(url)
	await router.isReady()

	const statusCode = router.currentRoute.value?.meta?.statusCode ?? 200

	// Default meta tags
	const metaTags = `
	<title>Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer</title>
	<meta name="description" content="Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.">
	<meta name="keywords" content="gold, silver, precious metals, gold buyer, silver buyer, appraisal, kit request">
	<meta property="og:title" content="Gold To Cash - Buy Gold and Silver | Professional Precious Metals Dealer">
	<meta property="og:description" content="Buy gold and silver at competitive prices. Professional precious metals dealer offering appraisal services, kit requests, and secure transactions.">
	<meta property="og:type" content="website">
`

	const appHtml = await renderToString(app)
	const state = pinia.state.value

	return { appHtml, state, metaTags, statusCode }
}


