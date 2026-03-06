<template>
	<BaseModal
		id="viewOrderFiles"
		title="View User Files"
		:dialog-style="{ maxWidth: '800px !important' }"
		@close="$emit('close')"
	>
		<table class="table table-striped">
			<tbody>
				<tr v-if="!files || files.length === 0">
					<td>No Order Files Available</td>
				</tr>
				<tr v-for="f in files" :key="f.id || f.url">
					<td>
						<div class="m-2 text-center file-card">
							<i class="far fa-file-image fa-3x"></i>
							<p class="mt-3 text-center">{{ f.title || f.name }}</p>
							<form :action="f.viewUrl || f.url" method="get" target="_blank" class="mb-3">
								<input type="submit" class="btn btn-sm btn-block btn-primary media-display-button-disable" value="View">
							</form>
						</div>
					</td>
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

<style scoped>
.file-card {
	min-width: 150px;
	padding: 1rem;
	border: 1px solid #ddd;
	border-radius: 0.25rem;
}
</style>



