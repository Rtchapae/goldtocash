import { get } from './client'

export const getBranches = async () => {
	return get('/admin/branches')
}




