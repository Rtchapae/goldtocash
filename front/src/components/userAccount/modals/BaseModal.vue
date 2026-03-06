<template>
	<Teleport to="body" v-if="isMounted">
		<div
			class="modal fade"
			:class="{ show: open }"
			:id="id"
			tabindex="-1"
			role="dialog"
			:aria-labelledby="`${id}Label`"
			:aria-hidden="open ? 'false' : 'true'"
			:style="{ display: open ? 'block' : 'none' }"
			@click.self="$emit('close')"
		>
			<div class="modal-dialog" role="document" :style="dialogStyle">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" :id="`${id}Label`">{{ title }}</h5>
						<button type="button" class="close" aria-label="Close" @click="$emit('close')">
							<span aria-hidden="true">×</span>
						</button>
					</div>

					<div class="modal-body">
						<slot />
					</div>

					<div class="modal-footer">
						<slot name="footer">
							<button type="button" class="btn btn-primary btn-fw" @click="$emit('close')">Close</button>
						</slot>
					</div>
				</div>
			</div>
		</div>

		<div v-if="open" class="modal-backdrop fade show" />
	</Teleport>
</template>

<script setup>
import { Teleport, watch, onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
	id: { type: String, required: true },
	title: { type: String, required: true },
	open: { type: Boolean, default: false },
	/**
	 * Optional inline styles for modal-dialog (legacy uses max-width overrides for some modals)
	 * Example: { maxWidth: '800px !important' }
	 */
	dialogStyle: { type: Object, default: () => ({}) },
})

defineEmits(['close'])

// SSR-safe: only render Teleport after client mount to avoid hydration mismatch
const isMounted = ref(false)

onMounted(() => {
	isMounted.value = true
})

// Best-effort body scroll lock (Bootstrap-like), without requiring bootstrap JS.
const applyBodyLock = (isOpen) => {
	if (typeof document === 'undefined') return
	document.body.classList.toggle('modal-open', isOpen)
	document.body.style.overflow = isOpen ? 'hidden' : ''
}

watch(
	() => props.open,
	(isOpen) => {
		// Only apply body lock after mount (client-side only)
		if (isMounted.value) {
			applyBodyLock(isOpen)
		}
	},
	{ immediate: true }
)

onBeforeUnmount(() => {
	// Safety: if modal is unmounted while open, ensure body isn't left locked.
	applyBodyLock(false)
})
</script>

<style scoped>
/* Ensure modal header layout matches Bootstrap-like behavior even without Bootstrap JS */
.modal-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.modal-title {
	margin: 0;
}

/* Our global styles define `button { background: ... }`; override for modal close button */
button.close {
	background: transparent;
	border: 0;
	padding: 0;
	margin: 0;
	color: inherit;
	line-height: 1;
	cursor: pointer;
}

button.close span[aria-hidden="true"] {
	display: block;
	font-size: 1.5rem;
	line-height: 1;
}
</style>


