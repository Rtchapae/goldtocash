<template>
	<BaseModal
		id="viewUserFiles"
		title="View Order Files"
		:dialog-style="{ maxWidth: '800px !important' }"
		@close="$emit('close')"
	>
		<table class="table table-striped">
			<tbody>
				<tr v-if="!files || files.length === 0">
					<td>No User Files Available</td>
				</tr>
				<tr v-for="f in files" :key="f.id || f.url">
					<td>{{ f.name || f.title || f.url }}</td>
				</tr>
			</tbody>
		</table>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	files: { type: Array, default: () => [] },
})

defineEmits(['close'])
</script>



