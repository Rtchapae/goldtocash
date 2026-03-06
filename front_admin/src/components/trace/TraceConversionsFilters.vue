<template>
	<div id="ConversionFilters" class="trace-events-filters">
		<label class="form-label">Source</label>
		<select
			:model-value="modelValue.source"
			name="source"
			class="form-control"
			@change="updateFilter('source', $event.target.value)"
		>
			<option value="">All sources</option>
			<option
				v-for="source in availableSources"
				:key="source"
				:value="source"
			>
				{{ source }}
			</option>
		</select>

		<label class="form-label">Campaign</label>
		<input
			:model-value="modelValue.campaign"
			type="text"
			name="campaign"
			class="form-control"
			placeholder="Enter campaign..."
			@input="updateFilter('campaign', $event.target.value)"
		/>

		<label class="form-label">Medium</label>
		<input
			:model-value="modelValue.medium"
			type="text"
			name="medium"
			class="form-control"
			placeholder="Enter medium..."
			@input="updateFilter('medium', $event.target.value)"
		/>

		<label class="form-label">Term</label>
		<input
			:model-value="modelValue.term"
			type="text"
			name="term"
			class="form-control"
			placeholder="Enter term..."
			@input="updateFilter('term', $event.target.value)"
		/>

		<label class="form-label">Content</label>
		<input
			:model-value="modelValue.content"
			type="text"
			name="content"
			class="form-control"
			placeholder="Enter content..."
			@input="updateFilter('content', $event.target.value)"
		/>

		<label class="form-label">Between</label>
		<div class="date-inputs-wrapper">
			<input
				:value="modelValue.dateFrom"
				type="date"
				name="date-from"
				class="form-control"
				@input="updateFilter('dateFrom', $event.target.value)"
			/>
			<input
				:value="modelValue.dateTo"
				type="date"
				name="date-to"
				class="form-control"
				@input="updateFilter('dateTo', $event.target.value)"
			/>
		</div>

		<hr class="filters-divider" />
		<div class="filters-actions">
			<button
				type="button"
				id="resetBtn"
				class="btn btn-outline-secondary btn-sm"
				@click="$emit('reset')"
				:disabled="loading"
			>
				Reset
			</button>
			<button
				type="button"
				id="filterBtn"
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

const emit = defineEmits(['update:modelValue', 'reset', 'filter'])

const updateFilter = (key, value) => {
	emit('update:modelValue', {
		...props.modelValue,
		[key]: value
	})
}
</script>

<style scoped>
</style>

