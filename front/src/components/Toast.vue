<template>
	<div
		:class="['toast', `toast-${type}`, { show: isVisible }]"
		role="alert"
		aria-live="assertive"
		aria-atomic="true"
	>
		<div class="toast-header">
			<strong class="me-auto">{{ typeLabel }}</strong>
			<button
				type="button"
				class="btn-close"
				aria-label="Close"
				@click="$emit('close')"
			></button>
		</div>
		<div class="toast-body">
			{{ message }}
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
	id: { type: String, required: true },
	message: { type: String, required: true },
	type: { type: String, default: 'info' }, // success, error, warning, info
})

defineEmits(['close'])

const isVisible = ref(false)

const typeLabel = computed(() => {
	const labels = {
		success: 'Success',
		error: 'Error',
		warning: 'Warning',
		info: 'Info',
	}
	return labels[props.type] || 'Info'
})

onMounted(() => {
	setTimeout(() => {
		isVisible.value = true
	}, 10)
})
</script>

<style scoped>
.toast {
	opacity: 0;
	transform: translateX(100%);
	transition: opacity 0.3s ease, transform 0.3s ease;
}

.toast.show {
	opacity: 1;
	transform: translateX(0);
}

.toast-success .toast-header {
	background-color: #d1e7dd;
	color: #0f5132;
	border-bottom-color: #badbcc;
}

.toast-error .toast-header {
	background-color: #f8d7da;
	color: #842029;
	border-bottom-color: #f5c2c7;
}

.toast-warning .toast-header {
	background-color: #fff3cd;
	color: #664d03;
	border-bottom-color: #ffecb5;
}

.toast-info .toast-header {
	background-color: #cfe2ff;
	color: #084298;
	border-bottom-color: #b6d4fe;
}

.toast-body {
	padding: 0.75rem;
}
</style>


