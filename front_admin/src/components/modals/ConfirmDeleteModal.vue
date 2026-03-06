<template>
	<div
		v-if="show"
		class="modal fade show"
		:class="{ show: show }"
		tabindex="-1"
		role="dialog"
		aria-labelledby="confirmDeleteModalLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
					<button
						type="button"
						class="close"
						aria-label="Close"
						@click="close"
					>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<p v-if="message">{{ message }}</p>
					<p v-else>Are you sure you want to delete this item?</p>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-secondary"
						@click="close"
					>
						Cancel
					</button>
					<button
						type="button"
						class="btn btn-danger"
						:disabled="isDeleting"
						@click="handleConfirm"
					>
						{{ isDeleting ? 'Deleting...' : 'Delete' }}
					</button>
				</div>
			</div>
		</div>
	</div>
	<div
		v-if="show"
		class="modal-backdrop fade show"
		@click="close"
	></div>
</template>

<script setup>
const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	message: {
		type: String,
		default: null
	},
	isDeleting: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['close', 'confirm'])

const close = () => {
	emit('close')
}

const handleConfirm = () => {
	emit('confirm')
}
</script>

<style scoped>
.modal-backdrop {
	position: fixed;
	top: 0;
	left: 0;
	z-index: 1040;
	width: 100vw;
	height: 100vh;
	background-color: #000;
	opacity: 0.5;
}

.modal.show {
	display: block;
}
</style>

