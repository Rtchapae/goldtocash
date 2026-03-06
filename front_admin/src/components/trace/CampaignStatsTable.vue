<template>
	<div class="campaign-stats-table-wrapper">
		<div v-if="loading" class="table-loading-overlay">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<table id="campaign-stats-table" class="table table-hover mb-0">
			<thead>
				<tr>
					<th
						class="sortable"
						:class="{ 'sorting-asc': sortField === 'campaign' && sortDirection === 'asc', 'sorting-desc': sortField === 'campaign' && sortDirection === 'desc' }"
						@click="handleSort('campaign')"
					>
						Campaign
					</th>
					<th
						class="sortable"
						:class="{ 'sorting-asc': sortField === 'traceCount' && sortDirection === 'asc', 'sorting-desc': sortField === 'traceCount' && sortDirection === 'desc' }"
						@click="handleSort('traceCount')"
					>
						Unique Visits
					</th>
					<th
						class="sortable"
						:class="{ 'sorting-asc': sortField === 'kitReqCount' && sortDirection === 'asc', 'sorting-desc': sortField === 'kitReqCount' && sortDirection === 'desc' }"
						@click="handleSort('kitReqCount')"
					>
						Leads
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-if="!loading && campaignStats.length === 0">
					<td colspan="3" class="text-center py-5 text-muted">
						No items.
					</td>
				</tr>
				<tr
					v-for="stat in sortedCampaignStats"
					:key="stat.campaign"
				>
					<td>{{ stat.campaign }}</td>
					<td>{{ stat.traceCount }}</td>
					<td>{{ stat.kitReqCount }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
	campaignStats: {
		type: Array,
		default: () => []
	},
	loading: {
		type: Boolean,
		default: false
	}
})

const sortField = ref(null)
const sortDirection = ref('asc')

const handleSort = (field) => {
	if (sortField.value === field) {
		sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
	} else {
		sortField.value = field
		sortDirection.value = 'asc'
	}
}

const sortedCampaignStats = computed(() => {
	if (!sortField.value || props.campaignStats.length === 0) {
		return props.campaignStats
	}

	const sorted = [...props.campaignStats].sort((a, b) => {
		const aVal = a[sortField.value]
		const bVal = b[sortField.value]

		if (typeof aVal === 'number' && typeof bVal === 'number') {
			return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal
		}

		const aStr = String(aVal || '')
		const bStr = String(bVal || '')

		if (sortDirection.value === 'asc') {
			return aStr.localeCompare(bStr)
		} else {
			return bStr.localeCompare(aStr)
		}
	})

	return sorted
})
</script>

<style scoped>
</style>

