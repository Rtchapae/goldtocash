<template>
	<div class="chart-wrapper">
		<div v-if="loading" class="chart-loading-overlay">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<div id="traffic-trace-table" ref="chartContainer"></div>
	</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import ApexCharts from 'apexcharts'

const props = defineProps({
	events: {
		type: Array,
		default: () => []
	},
	loading: {
		type: Boolean,
		default: false
	}
})

const chartContainer = ref(null)
let chart = null

const initChart = () => {
	if (!chartContainer.value) {
		return
	}

	const chartOptions = {
		series: [],
		chart: {
			type: 'line',
			height: 400,
			toolbar: { show: false },
			zoom: { enabled: false }
		},
		stroke: {
			curve: 'smooth',
			width: 2
		},
		xaxis: {
			type: 'datetime',
			labels: {
				format: 'MMM dd'
			}
		},
		yaxis: {
			title: {
				text: 'Number of Events'
			}
		},
		legend: {
			position: 'bottom',
			horizontalAlign: 'center'
		},
		grid: {
			show: true,
			borderColor: '#E5E7EB',
			strokeDashArray: 0,
			position: 'back',
			xaxis: { lines: { show: false } },
			yaxis: { lines: { show: true } }
		},
		tooltip: {
			shared: true,
			intersect: false
		}
	}

	chart = new ApexCharts(chartContainer.value, chartOptions)
	chart.render()
}

const updateChart = () => {
	if (!chart) return

	if (!props.events.length) {
		chart.updateSeries([])
		return
	}

	const dateBag = {}
	const eventTypes = []
	let dateMin = null
	let dateMax = null

	props.events.forEach(event => {
		const dateInt = dateStringAsDateInt(event.formatted_date)
		if (dateInt < dateMin || dateMin === null) {
			dateMin = dateInt
		}
		if (dateInt > dateMax || dateMax === null) {
			dateMax = dateInt
		}
	})

	props.events.forEach(event => {
		const dateInt = dateStringAsDateInt(event.formatted_date)
		if (!dateBag[dateInt]) {
			dateBag[dateInt] = {}
		}
		dateBag[dateInt][event.name] = event.cnt
		if (!eventTypes.includes(event.name)) {
			eventTypes.push(event.name)
		}
	})

	let iteratorMin = dateMin
	while (iteratorMin <= dateMax) {
		if (!dateBag[iteratorMin]) {
			dateBag[iteratorMin] = {}
		}
		const date = dateIntAsDate(iteratorMin)
		date.setDate(date.getDate() + 1)
		const year = date.getFullYear()
		const month = String(date.getMonth() + 1).padStart(2, '0')
		const day = String(date.getDate()).padStart(2, '0')
		iteratorMin = parseInt(`${year}${month}${day}`)
	}

	const sortedDates = Object.keys(dateBag).map(Number).sort((a, b) => a - b)
	const series = []

	eventTypes.forEach(eventType => {
		const data = sortedDates.map(date => {
			const dateObj = dateIntAsDate(date)
			const timestamp = new Date(Date.UTC(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate())).getTime()
			return [timestamp, dateBag[date][eventType] || null]
		})

		series.push({
			name: eventType,
			data: data
		})
	})

	chart.updateSeries(series)
}

const dateStringAsDateInt = (dateString) => {
	return parseInt(dateString.replaceAll('-', ''))
}

const dateIntAsDate = (dateInt) => {
	const dateParts = []
	let num = dateInt
	while (dateParts.length < 3) {
		const parts = (num / (dateParts.length === 2 ? 10000 : 100)).toString().split('.')
		dateParts.unshift(parts[1].length === 1 ? `${parts[1]}0` : parts[1])
		num = parts[0]
	}
	return new Date(dateParts[0], dateParts[1] - 1, dateParts[2])
}

watch(() => props.events, () => {
	updateChart()
}, { deep: true })

onMounted(() => {
	initChart()
	updateChart()
})

onBeforeUnmount(() => {
	if (chart) {
		chart.destroy()
		chart = null
	}
})
</script>

<style scoped>
</style>

