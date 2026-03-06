import { get } from './client.js'

export const getCountries = async () => {
	return get('/countries')
}




