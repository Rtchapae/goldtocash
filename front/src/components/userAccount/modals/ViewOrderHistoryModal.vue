<template>
	<BaseModal
		id="viewOrderHistory"
		title="View Order History"
		:dialog-style="{ maxWidth: '800px !important' }"
		@close="$emit('close')"
	>
		<table class="table table-striped">
			<tbody>
				<tr v-if="!history || history.length === 0">
					<td>No History Records Available</td>
				</tr>
				<tr v-for="(row, idx) in history" :key="idx">
					<td>{{ row }}</td>
				</tr>
			</tbody>
		</table>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	history: { type: Array, default: () => [] },
})

defineEmits(['close'])
</script>



