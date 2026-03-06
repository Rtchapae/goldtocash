import axios from 'axios'

const httpClient = axios.create({
	baseURL: 'http://localhost:8000/api/v1',
	timeout: 2000,
})

async function testSeoApi() {
	try {
		console.log('Testing SEO API from SSR...')
		const response = await httpClient.get('/seo', {
			params: { route_name: 'home', page_url: '/' }
		})
		console.log('Success:', response.data)
	} catch (error) {
		console.error('Error:', error.message)
	}
}

testSeoApi()
