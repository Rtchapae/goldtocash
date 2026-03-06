import { post, get, downloadFile } from './client.js'

export const registerKit = async (data) => {
	return post('/kit/register', data, {}, true)
}

export const getPrintLabel = async (orderId) => {
	return downloadFile(`/orders/${orderId}/print-label`)
}

export async function confirmAccountCreation(orderId) {
	return post(`/orders/${orderId}/confirm-account`)
}
