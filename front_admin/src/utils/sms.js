
export const getReceiptBadgeClass = (status) => {
	if (!status) return 'bg-light text-dark border'
	
	switch (status.toLowerCase()) {
		case 'delivered':
			return 'bg-success text-white'
		case 'undelivered':
			return 'bg-danger text-white'
		default:
			return 'bg-light text-dark border'
	}
}

export const truncateMessage = (message) => {
	if (!message) return '-'
	if (message.length <= 100) return message
	return message.substring(0, 100) + '...'
}

