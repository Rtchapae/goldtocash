import { createApp } from './app'
import { createMemoryHistory } from 'vue-router'
import { renderToString } from '@vue/server-renderer'
import { getStaticSitemapPaths } from './router'
import { buildSsrMetaTags } from './utils/ssrSeo.js'
import { isMobileUserAgent } from './utils/device.js'

export { getStaticSitemapPaths }

export async function render(url, context = {}) {
	const { app, router, pinia } = createApp(createMemoryHistory())
	const ssrIsMobile = isMobileUserAgent(context.userAgent ?? '')
	app.provide('ssrIsMobile', ssrIsMobile)

	await router.push(url)
	await router.isReady()

	const statusCode = router.currentRoute.value?.meta?.statusCode ?? 200
	const current = router.currentRoute.value
	const metaTags = await buildSsrMetaTags(
		{ name: current?.name, path: current?.path, params: current?.params },
		{ apiBaseUrl: context.ssrApiBaseUrl }
	)

	const appHtml = await renderToString(app)
	const state = pinia.state.value

	return { appHtml, state, metaTags, statusCode, ssrIsMobile }
}


