import { ref, onBeforeUnmount } from 'vue'
import ApexCharts from 'apexcharts'
import { formatYAxisValue, formatTooltipValue, formatMonthData } from '@/utils/chartFormatters'

export function useChartInitialization() {
	const charts = ref([])
	const isInitialized = ref(false)

	const createLineChartOptions = (data, labels, color, name) => ({
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
		xaxis: { categories: labels },
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
		grid: {
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
		},
		tooltip: {
			y: {
				formatter: formatTooltipValue
			}
		}
	})

	const createBarChartOptions = (data, labels, color, name) => ({
		series: [{
			name,
			data
		}],
		chart: {
			type: 'bar',
			height: 300,
			toolbar: { show: false }
		},
		xaxis: { categories: labels },
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
		grid: {
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
		},
		tooltip: {
			y: {
				formatter: formatTooltipValue
			}
		}
	})

	const initChart = (element, options) => {
		const el = typeof element === 'string' ? document.querySelector(element) : element
		if (!el) {
			console.warn('Chart element not found:', element)
			return null
		}

		const chart = new ApexCharts(el, options)
		chart.render()
		charts.value.push(chart)
		return chart
	}

	const destroyAll = () => {
		charts.value.forEach(chart => {
			try {
				if (chart && typeof chart.destroy === 'function') {
					chart.destroy()
				}
			} catch (e) {
				console.warn('Error destroying chart:', e)
			}
		})
		charts.value = []
		isInitialized.value = false
	}

	const clearChart = (element) => {
		const el = typeof element === 'string' ? document.querySelector(element) : element
		if (el) {
			el.innerHTML = ''
		}
	}

	onBeforeUnmount(() => {
		destroyAll()
	})

	return {
		charts,
		isInitialized,
		createLineChartOptions,
		createBarChartOptions,
		initChart,
		destroyAll,
		clearChart,
		formatMonthData
	}
}

