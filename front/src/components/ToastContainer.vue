<template>
	<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
		<Toast
			v-for="toast in toasts"
			:key="toast.id"
			:id="toast.id"
			:message="toast.message"
			:type="toast.type"
			@close="removeToast(toast.id)"
		/>
	</div>
</template>

<script setup>
import { computed } from 'vue'
import { useToastStore } from '@/stores/toast'
import Toast from './Toast.vue'

const toastStore = useToastStore()

const toasts = computed(() => toastStore.toasts)

const removeToast = (id) => {
	toastStore.remove(id)
}
</script>

<style scoped>
.toast-container {
	max-width: 350px;
}

.toast-container .toast {
	margin-bottom: 0.5rem;
}
</style>


