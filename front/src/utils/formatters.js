export function formatDateMMDDYYYY(date) {
	if (!date) return 'N/A'
	const d = new Date(date)
	return d.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
}

export function formatDateTime(date) {
	if (!date) return 'N/A'
	const d = new Date(date)
	const dateStr = d.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
	const timeStr = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })
	return `${dateStr} ${timeStr}`
}

export function formatMessageTime(date) {
	if (!date) return ''
	const d = new Date(date)
	const now = new Date()
	const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
	const messageDate = new Date(d.getFullYear(), d.getMonth(), d.getDate())
	
	if (messageDate.getTime() === today.getTime()) {
		return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
	}
	
	const yesterday = new Date(today)
	yesterday.setDate(yesterday.getDate() - 1)
	if (messageDate.getTime() === yesterday.getTime()) {
		return `Yesterday ${d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}`
	}
	
	const dateStr = d.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' })
	const timeStr = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
	return `${dateStr} ${timeStr}`
}

export function formatConversationDate(date) {
	if (!date) return ''
	const d = new Date(date)
	const now = new Date()
	const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
	const messageDate = new Date(d.getFullYear(), d.getMonth(), d.getDate())
	
	if (messageDate.getTime() === today.getTime()) {
		return 'Today'
	}
	
	const yesterday = new Date(today)
	yesterday.setDate(yesterday.getDate() - 1)
	if (messageDate.getTime() === yesterday.getTime()) {
		return 'Yesterday'
	}
	
	return d.toLocaleDateString('en-US', { 
		weekday: 'long',
		month: 'long', 
		day: 'numeric',
		year: d.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
	})
}

export function formatAmount2dp(amount) {
	if (!amount) return '0.00'
	return parseFloat(amount).toFixed(2)
}

export function formatPhoneUS(phone) {
	if (!phone) return 'N/A'
	const cleaned = String(phone).replace(/\D/g, '')
	if (cleaned.length === 10) {
		return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`
	}
	return String(phone)
}


