
export const formatCurrency = (value) => {
	return new Intl.NumberFormat('en-US', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	}).format(value)
}

export const formatMonthData = (data, key = 'total') => {
	const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	const monthMap = {}
	
	data.forEach(item => {
		const [year, month] = item.month.split('-')
		const monthIndex = parseInt(month) - 1
		monthMap[item.month] = item[key] || 0
	})
	
	const last12Months = []
	for (let i = 11; i >= 0; i--) {
		const date = new Date()
		date.setMonth(date.getMonth() - i)
		const year = date.getFullYear()
		const month = String(date.getMonth() + 1).padStart(2, '0')
		const monthKey = `${year}-${month}`
		last12Months.push({
			label: monthNames[date.getMonth()],
			value: monthMap[monthKey] || 0
		})
	}
	
	return {
		labels: last12Months.map(m => m.label),
		data: last12Months.map(m => m.value)
	}
}

export const formatYAxisValue = (value) => {
	if (value >= 1000000) {
		return (value / 1000000).toFixed(1) + 'M'
	}
	if (value >= 1000) {
		return (value / 1000).toFixed(1) + 'K'
	}
	return value.toString()
}

export const formatTooltipValue = (value) => {
	return value.toFixed(1)
}

