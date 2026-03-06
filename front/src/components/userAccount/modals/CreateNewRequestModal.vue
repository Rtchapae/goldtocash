<template>
	<BaseModal
		id="createNewRequest"
		title="Create new Kit Request"
		:dialog-style="{ maxWidth: '800px !important' }"
		:open="open"
		@close="$emit('close')"
	>
		<div>
			Do you want to send a new submission kit request ?
		</div>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')" :disabled="isLoading">No</button>
			<button type="button" class="btn btn-danger btn-fw" @click="$emit('confirm')" :disabled="isLoading">
				<span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
				{{ isLoading ? 'Creating...' : 'Yes' }}
			</button>
		</template>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	open: { type: Boolean, default: false },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['close', 'confirm'])
</script>


