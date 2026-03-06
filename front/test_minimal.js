import express from 'express'
import axios from 'axios'

const app = express()
const port = 5176

const httpClient = axios.create({
	baseURL: 'http://localhost:8000/api/v1',
	timeout: 2000,
})

app.get('/', async (req, res) => {
	try {
		console.log('Testing SEO API...')
		const response = await httpClient.get('/seo', {
			params: { route_name: 'home', page_url: '/' }
		})

		const seoData = response.data
		const html = `
<!DOCTYPE html>
<html>
<head>
	<title>${seoData.title || 'Test'}</title>
	<meta name="description" content="${seoData.description || ''}">
</head>
<body>
	<h1>SEO Test</h1>
	<p>Title: ${seoData.title}</p>
	<p>Description: ${seoData.description}</p>
</body>
</html>`

		res.send(html)
	} catch (error) {
		console.error('Error:', error.message)
		res.status(500).send('Error')
	}
})

app.listen(port, () => {
	console.log(`Test server running at http://localhost:${port}`)
})
