
import { formatYAxisValue, formatTooltipValue } from './chartFormatters'
const getCommonGrid = () => ({
	show: true,
	borderColor: '#E5E7EB',
	strokeDashArray: 0,
	position: 'back',
	xaxis: { lines: { show: false } },
	yaxis: {
		lines: { show: true },
		labels: {
			formatter: formatYAxisValue
		}
	}
})

const getCommonTooltip = () => ({
	y: {
		formatter: formatTooltipValue
	}
})

export const createLineChartConfig = ({ name, data, categories, color }) => ({
	series: [{
		name,
		data
	}],
	chart: {
		type: 'line',
		height: 300,
		toolbar: { show: false }
	},
	stroke: { curve: 'smooth', width: 3 },
	xaxis: { categories },
	yaxis: {
		labels: {
			formatter: formatYAxisValue
		}
	},
	colors: [color],
	fill: {
		type: 'gradient',
		gradient: {
			shade: 'light',
			type: 'vertical',
			shadeIntensity: 0.3,
			gradientToColors: [color],
			inverseColors: false,
			opacityFrom: 0.7,
			opacityTo: 0.1,
			stops: [0, 100]
		}
	},
	grid: getCommonGrid(),
	tooltip: getCommonTooltip()
})

export const createBarChartConfig = ({ name, data, categories, color }) => ({
	series: [{
		name,
		data
	}],
	chart: {
		type: 'bar',
		height: 300,
		toolbar: { show: false }
	},
	xaxis: { categories },
	colors: [color],
	plotOptions: {
		bar: {
			borderRadius: 4,
			columnWidth: '60%'
		}
	},
	dataLabels: {
		enabled: false
	},
	grid: getCommonGrid(),
	tooltip: getCommonTooltip()
})

