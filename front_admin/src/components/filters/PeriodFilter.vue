<template>
	<div :class="wrapperClass">
		<div class="btn-group period-btn-group" role="group">
			<button
				v-for="period in periods"
				:key="period.value"
				type="button"
				:class="['btn', period.value === modelValue ? 'btn-secondary' : 'btn-outline-secondary']"
				@click="handlePeriodChange(period.value)"
			>
				{{ period.label }}
			</button>
		</div>
		<div v-if="modelValue === 'custom'" class="custom-search mt-3">
			<form @submit.prevent="handleCustomSubmit" class="d-flex gap-2">
				<input
					v-model="fromDate"
					name="from"
					type="date"
					class="form-control"
					required
				/>
				<input
					v-model="toDate"
					name="to"
					type="date"
					class="form-control"
					required
				/>
				<button type="submit" class="btn btn-primary">
					Set
				</button>
			</form>
		</div>
	</div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { PERIODS } from '@/config/orders'

const props = defineProps({
	modelValue: {
		type: String,
		required: true
	},
	from: {
		type: String,
		default: null
	},
	to: {
		type: String,
		default: null
	},
	/** When true, omit Bootstrap column classes — use beside search / toolbar rows. */
	toolbar: {
		type: Boolean,
		default: false
	}
})

const wrapperClass = computed(() => {
	if (props.toolbar) {
		return 'period-filter-wrapper period-filter-toolbar'
	}
	return 'col-lg-4 ms-auto period-filter-wrapper'
})

const emit = defineEmits(['update:modelValue', 'update:from', 'update:to', 'change'])

const periods = PERIODS
const fromDate = ref(props.from || '')
const toDate = ref(props.to || '')

watch(() => props.modelValue, (newValue) => {
	if (newValue === 'custom' && !fromDate.value && !toDate.value) {
		const today = new Date()
		const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate())
		
		fromDate.value = formatDate(lastMonth)
		toDate.value = formatDate(today)
	}
}, { immediate: true })

watch(() => props.from, (newValue) => {
	if (newValue !== fromDate.value) {
		fromDate.value = newValue || ''
	}
})

watch(() => props.to, (newValue) => {
	if (newValue !== toDate.value) {
		toDate.value = newValue || ''
	}
})

const formatDate = (date) => {
	const d = date instanceof Date ? date : new Date(date)
	const year = d.getFullYear()
	const month = String(d.getMonth() + 1).padStart(2, '0')
	const day = String(d.getDate()).padStart(2, '0')
	return `${year}-${month}-${day}`
}

const handlePeriodChange = (period) => {
	emit('update:modelValue', period)
	emit('change', { period, from: null, to: null })
}

const handleCustomSubmit = () => {
	if (fromDate.value && toDate.value) {
		emit('update:from', fromDate.value)
		emit('update:to', toDate.value)
		emit('change', { period: 'custom', from: fromDate.value, to: toDate.value })
	}
}
</script>

<style scoped>
.custom-search {
	width: 100%;
	margin-top: 0.75rem;
}

.custom-search form {
	align-items: stretch;
	height: 100%;
}

.custom-search .form-control {
	flex: 1;
	height: calc(1.5em + 0.75rem + 2px);
}

.custom-search .btn {
	flex-shrink: 0;
	height: calc(1.5em + 0.75rem + 2px);
}

.period-filter-toolbar {
	flex: 0 1 auto;
	min-width: 0;
	max-width: 100%;
}

.period-filter-toolbar .period-btn-group {
	flex-wrap: wrap;
	justify-content: flex-end;
}
</style>

