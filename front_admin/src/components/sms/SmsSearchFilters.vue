<template>
	<div id="SmsNavigateSearchManager">
		<div class="d-flex align-items-end flex-wrap gap-3">
			<div class="search-filter">
				<label>From</label>
				<input
					v-model="localFilters.from"
					type="text"
					class="form-control"
					name="from"
					placeholder="Phone number"
					@input="updateFilters"
				/>
			</div>

			<div class="search-filter">
				<label>To</label>
				<input
					v-model="localFilters.to"
					type="text"
					class="form-control"
					name="to"
					placeholder="Phone number"
					@input="updateFilters"
				/>
			</div>

			<div class="search-filter">
				<label>MID</label>
				<input
					v-model="localFilters.mid"
					type="text"
					class="form-control"
					name="mid"
					placeholder="Message ID"
					@input="updateFilters"
				/>
			</div>

			<div class="search-filter">
				<label class="invisible">Actions</label>
				<div class="d-flex gap-2">
					<button
						type="button"
						id="search-submit-btn"
						class="btn btn-info"
						:disabled="loading"
						@click="$emit('search')"
					>
						<i :class="['fa', 'me-2', loading ? 'fa-spin fa-spinner' : 'fa-search']"></i>
						Search
					</button>
					<button
						v-if="hasActiveFilters"
						type="button"
						class="btn btn-outline-secondary"
						:disabled="loading"
						@click="handleClear"
					>
						<i class="fa fa-times me-2"></i>
						Clear
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
	filters: {
		type: Object,
		required: true
	},
	loading: {
		type: Boolean,
		default: false
	},
	hasActiveFilters: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['update:filters', 'search', 'clear'])

const localFilters = ref({ ...props.filters })

watch(() => props.filters, (newFilters) => {
	localFilters.value = { ...newFilters }
}, { deep: true })

const updateFilters = () => {
	emit('update:filters', { ...localFilters.value })
}

const handleClear = () => {
	localFilters.value = {
		from: '',
		to: '',
		mid: ''
	}
	emit('update:filters', { ...localFilters.value })
	emit('clear')
}
</script>
