import { get } from './client'

export const getCountries = async () => {
	return get('/admin/countries')
}




