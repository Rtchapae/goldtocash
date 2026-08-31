
/** Full API label → short label for orders table */
const STATUS_SHORT_LABELS = {
	'Kit Requested': 'Request',
	'In Transit': 'Shipped',
	'Items Received': 'Received',
	'Appraising': null,
	'Appraisal': null,
	'Offer Sent': 'Offer Sent',
	'Offer Accepted': 'Accepted',
	'Paid': 'Paid',
	'Offer Denied': 'Declined',
	'Items Sent Back': 'Returned',
	'No Sale': 'No Sale',
}

const STATUS_BADGE_MAP = {
	'Kit Requested': 'bg-warning',
	'In Transit': 'bg-info',
	'Items Received': 'bg-primary',
	'Appraising': 'bg-secondary',
	'Appraisal': 'bg-secondary',
	'Offer Sent': 'bg-success',
	'Offer Accepted': 'bg-success',
	'Paid': 'bg-warning',
	'Offer Denied': 'bg-danger',
	'Items Sent Back': 'bg-info',
	'No Sale': 'bg-secondary',
	// short labels (badges after formatOrderStatusLabel)
	Request: 'bg-warning',
	Shipped: 'bg-info',
	Received: 'bg-primary',
	'Offer Sent': 'bg-success',
	Accepted: 'bg-success',
	Paid: 'bg-warning',
	Declined: 'bg-danger',
	Returned: 'bg-info',
	'No Sale': 'bg-secondary',
}

const DEFAULT_BADGE_CLASS = 'bg-secondary'

/** Short label for table; null/Appraisal → em dash */
export const formatOrderStatusLabel = (statusText) => {
	if (!statusText || typeof statusText !== 'string') {
		return 'Unknown'
	}
	if (Object.prototype.hasOwnProperty.call(STATUS_SHORT_LABELS, statusText)) {
		const short = STATUS_SHORT_LABELS[statusText]
		return short === null ? '—' : short
	}
	return statusText
}

export const isOrderStatusHidden = (statusText) =>
	formatOrderStatusLabel(statusText) === '—'

export const getStatusBadgeClass = (statusText) => {
	if (!statusText || typeof statusText !== 'string') {
		return DEFAULT_BADGE_CLASS
	}
	const short = formatOrderStatusLabel(statusText)
	if (short !== '—' && STATUS_BADGE_MAP[short]) {
		return STATUS_BADGE_MAP[short]
	}
	return STATUS_BADGE_MAP[statusText] || DEFAULT_BADGE_CLASS
}
