<template>
	<div id="campaign-stats-filters" class="campaign-stats-filters">
		<label class="form-label">Source</label>
		<select
			:value="modelValue.source"
			name="source"
			class="form-control"
			@change="updateFilter('source', $event.target.value)"
		>
			<option value="">Select source...</option>
			<option
				v-for="source in availableSources"
				:key="source"
				:value="source"
			>
				{{ source }}
			</option>
		</select>

		<label class="form-label">From</label>
		<input
			:value="modelValue.dateFrom"
			type="date"
			name="dateFrom"
			class="form-control"
			@input="updateFilter('dateFrom', $event.target.value)"
		/>

		<label class="form-label">To</label>
		<input
			:value="modelValue.dateTo"
			type="date"
			name="dateTo"
			class="form-control"
			@input="updateFilter('dateTo', $event.target.value)"
		/>

		<div class="filters-actions">
			<button
				type="button"
				class="btn btn-info btn-sm"
				@click="$emit('filter')"
				:disabled="loading"
			>
				Filter
			</button>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	modelValue: {
		type: Object,
		required: true
	},
	availableSources: {
		type: Array,
		default: () => []
	},
	loading: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['update:modelValue', 'filter'])

const updateFilter = (key, value) => {
	emit('update:modelValue', {
		...props.modelValue,
		[key]: value
	})
}
</script>

<style scoped>
</style>

