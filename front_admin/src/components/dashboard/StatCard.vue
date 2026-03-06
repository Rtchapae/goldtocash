<template>
	<div class="card px-24 py-16 shadow-none radius-8 border h-100" :class="gradientClass">
			<div class="card-body p-0">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
					<div class="d-flex align-items-center">
						<div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
							<span class="mb-0 w-40-px h-40-px flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0" :class="iconBgClass">
								<iconify-icon :icon="icon" :class="iconClass"></iconify-icon>
							</span>
						</div>
						<div>
							<span class="mb-2 fw-medium text-secondary-light text-md">{{ label }}</span>
							<h6 class="fw-semibold my-1">
								<template v-if="isCurrency">${{ formattedValue }}</template>
								<template v-else>{{ value }}</template>
							</h6>
							<p v-if="changePercent !== null" class="text-sm mb-0">
								<span 
									class="px-1 rounded-2 fw-medium text-sm"
									:class="changePercentClass"
								>
									{{ changePercent > 0 ? '+' : '' }}{{ changePercent }}%
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
</template>

<script setup>
import { computed } from 'vue'
import { formatCurrency } from '@/utils/chartFormatters'

const props = defineProps({
	label: {
		type: String,
		required: true
	},
	value: {
		type: [Number, String],
		default: 0
	},
	icon: {
		type: String,
		required: true
	},
	iconBgClass: {
		type: String,
		required: true
	},
	iconClass: {
		type: String,
		default: 'icon'
	},
	gradientClass: {
		type: String,
		default: 'bg-gradient-start-3'
	},
	isCurrency: {
		type: Boolean,
		default: false
	},
	changePercent: {
		type: Number,
		default: null
	},
})

const formattedValue = computed(() => {
	if (props.isCurrency) {
		return formatCurrency(props.value || 0)
	}
	return props.value
})

const changePercentClass = computed(() => {
	if (props.changePercent === null) return ''
	return props.changePercent >= 0 
		? 'bg-success-focus text-success-main'
		: 'bg-danger-focus text-danger-main'
})
</script>

