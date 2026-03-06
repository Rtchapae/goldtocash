
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
}
const DEFAULT_BADGE_CLASS = 'bg-secondary'

export const getStatusBadgeClass = (statusText) => {
	if (!statusText || typeof statusText !== 'string') {
		return DEFAULT_BADGE_CLASS
	}
	
	return STATUS_BADGE_MAP[statusText] || DEFAULT_BADGE_CLASS
}

