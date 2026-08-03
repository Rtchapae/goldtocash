
export const formatDateTime = (dateTime) => {
	if (!dateTime) return ''

	if (typeof dateTime === 'string' && /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(dateTime)) {
		return dateTime
	}
	
	const date = new Date(dateTime)
	if (isNaN(date.getTime())) return dateTime
	
	const year = date.getFullYear()
	const month = String(date.getMonth() + 1).padStart(2, '0')
	const day = String(date.getDate()).padStart(2, '0')
	const hours = String(date.getHours()).padStart(2, '0')
	const minutes = String(date.getMinutes()).padStart(2, '0')
	const seconds = String(date.getSeconds()).padStart(2, '0')
	
	return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

export const formatDate = (date, format = 'YYYY-MM-DD') => {
	if (!date) return ''
	
	const dateObj = new Date(date)
	if (isNaN(dateObj.getTime())) return date
	
	const year = dateObj.getFullYear()
	const month = String(dateObj.getMonth() + 1).padStart(2, '0')
	const day = String(dateObj.getDate()).padStart(2, '0')
	
	if (format === 'MM/DD/YYYY') {
		return `${month}/${day}/${year}`
	}
	
	return `${year}-${month}-${day}`
}

/**
 * US short date MM-DD-YY (e.g. 08-03-26).
 * Parses Laravel naive `Y-m-d H:i:s` (app timezone) without JS timezone shifting,
 * so Aug 1 00:30 LA does not become Jul 31 in the browser.
 */
export const formatDateMMDDYY = (dateTime) => {
	if (!dateTime) return ''

	const raw = String(dateTime).trim()
	const mysql = raw.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}):(\d{2}))?/)
	if (mysql) {
		return `${mysql[2]}-${mysql[3]}-${mysql[1].slice(-2)}`
	}

	const dateObj = new Date(dateTime)
	if (isNaN(dateObj.getTime())) {
		return raw
	}

	const month = String(dateObj.getMonth() + 1).padStart(2, '0')
	const day = String(dateObj.getDate()).padStart(2, '0')
	const yy = String(dateObj.getFullYear()).slice(-2)

	return `${month}-${day}-${yy}`
}

/** Local time HH:mm:ss */
export const formatTimeOnly = (dateTime) => {
	if (!dateTime) return ''

	const dateObj = new Date(dateTime)
	if (isNaN(dateObj.getTime())) {
		return ''
	}

	const hours = String(dateObj.getHours()).padStart(2, '0')
	const minutes = String(dateObj.getMinutes()).padStart(2, '0')
	const seconds = String(dateObj.getSeconds()).padStart(2, '0')

	return `${hours}:${minutes}:${seconds}`
}


