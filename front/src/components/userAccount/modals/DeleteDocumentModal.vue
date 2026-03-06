<template>
	<BaseModal id="mainFileDeleting" title="Remove a Document" @close="$emit('close')">
		<div>
			Are you sure you want to remove this document?
		</div>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')">Cancel</button>
			<button type="button" class="btn btn-danger btn-fw" @click="$emit('confirm', fileId)">Remove</button>
		</template>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	fileId: { type: [String, Number], default: null },
})

defineEmits(['close', 'confirm'])
</script>



