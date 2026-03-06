<template>
	<div id="conversion-results" class="trace-conversions-report">
		<div v-if="isLoading" class="conversion-loading">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<template v-else-if="conversionItems.length">
			<div
				v-for="(item, index) in conversionItems"
				:key="item.key"
				:class="['conversion-result', { 'new-group': item.isNewGroup }]"
			>
				<span>{{ item.label }}</span>
				<span>{{ item.value }}{{ item.isPercentage ? '%' : '' }}</span>
			</div>
		</template>
		<div v-else class="conversion-empty">
			<h5 class="text-center mt-5">No data available</h5>
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	conversions: {
		type: Object,
		default: () => ({})
	},
	isLoading: {
		type: Boolean,
		default: false
	}
})

const formatName = (name) => {
	return name
		.replace(/([A-Z])/g, ' $1')
		.replace(/^./, str => str.toUpperCase())
		.trim()
}

const needsPercentSymbol = (key) => {
	return key.includes('To')
}

const conversionItems = computed(() => {
	if (!props.conversions || Object.keys(props.conversions).length === 0) {
		return []
	}

	const items = []
	const order = [
		'traceCount',
		'kitReqCount',
		'itemsReceivedCount',
		'offerAcceptedCount',
		'traceToKit',
		'traceToReceived',
		'kitToReceived',
		'traceToAccepted',
		'kitToAccepted',
		'receivedToAccepted'
	]

	let lastSuffix = ''
	let lastHadTo = false
	order.forEach((key, index) => {
		if (props.conversions[key] === undefined) {
			return
		}

		const splits = key.split(/(?=[A-Z])/)
		const currentSuffix = splits[splits.length - 1] || ''
		const hasTo = key.includes('To')
		
		const isNewGroup = (hasTo && !lastHadTo && index > 0) || 
		                   (hasTo && lastHadTo && currentSuffix !== lastSuffix && lastSuffix !== '')

		items.push({
			key,
			label: formatName(key),
			value: props.conversions[key],
			isPercentage: needsPercentSymbol(key),
			isNewGroup
		})

		lastSuffix = currentSuffix
		lastHadTo = hasTo
	})

	return items
})
</script>

<style scoped>
</style>

