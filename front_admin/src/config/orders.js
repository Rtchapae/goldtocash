export const DEFAULT_PER_PAGE = 15
/** Page size for /orders infinite scroll only */
export const ORDERS_INFINITE_SCROLL_PER_PAGE = 50
export const DEFAULT_PAGE = 1

export const DEFAULT_PERIOD = 'currentmonth'
export const DEFAULT_FILTER_VALUE = 'any'

export const DEFAULT_SORT_DIR = 'desc'
export const SORT_DIR_ASC = 'asc'
export const SORT_DIR_DESC = 'desc'

export const SEARCH_DEBOUNCE_DELAY = 500

export const PAGE_SIZE_OPTIONS = [10, 15, 25, 50, 100]

export const PERIODS = [
	{ value: 'all', label: 'All' },
	{ value: 'today', label: 'Today' },
	{ value: 'yesterday', label: 'Yesterday' },
	{ value: 'currentmonth', label: 'Current Month' },
	{ value: 'lastmonth', label: 'Last Month' },
	{ value: 'custom', label: 'Custom' },
]

export const ORDER_COLUMNS = [
	{ key: 'name', label: 'Name', sortable: true, class: 'col-name' },
	{ key: 'url', label: 'URL', sortable: false },
	{ key: 'source', label: 'Source', sortable: false, class: 'col-source' },
	{ key: 'email', label: 'Email', sortable: true },
	{ key: 'phone', label: 'Phone', sortable: true },
	{ key: 'order_number', label: 'Order Number', sortable: true },
	{ key: 'date_created', label: 'Date Created', sortable: true },
	{ key: 'amount', label: 'Amount', sortable: true },
	{ key: 'status', label: 'Status', sortable: true, class: 'col-status' },
	{ key: 'order_type', label: 'Order Type', sortable: true },
	{ key: 'branch', label: 'Branch', sortable: true },
	{ key: 'actions', label: 'Action', sortable: false, class: 'col-actions' },
]

export const STATUS_BADGE_CLASSES = {
	0: 'badge-warning', // Kit Requested
	1: 'badge-info', // In Transit
	2: 'badge-primary', // Items Received
	3: 'badge-secondary', // Appraising
	4: 'badge-success', // Offer Sent
	5: 'badge-success', // Offer Accepted
	6: 'badge-warning', // Paid
	7: 'badge-danger', // Offer Denied
	8: 'badge-info', // Items Sent Back
	9: 'badge-secondary', // No Sale
	10: 'badge-info', // Items Sent Back (alt)
}

export const DEFAULT_STATUS_BADGE_CLASS = 'badge-secondary'

export const STATUS_TEXT_MAP = {
	0: 'Kit Requested',
	1: 'In Transit',
	2: 'Items Received',
	3: 'Appraising',
	4: 'Offer Sent',
	5: 'Offer Accepted',
	6: 'Paid',
	7: 'Offer Denied',
	8: 'Items Sent Back',
	9: 'No Sale',
	10: 'Items Sent Back',
}

export const DEFAULT_STATUS_TEXT = 'Unknown'

