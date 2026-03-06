import { get } from './client'

export const getStatesAndCities = async () => {
	return get('/admin/states-cities')
}

export const getCitiesByState = async (state) => {
	return get(`/admin/states-cities/${encodeURIComponent(state)}/cities`)
}




