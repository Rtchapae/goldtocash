import { get } from './client.js'

export const getStatesAndCities = async () => {
	return get('/states-cities')
}

export const getCitiesByState = async (state) => {
	return get(`/states-cities/${encodeURIComponent(state)}/cities`)
}


