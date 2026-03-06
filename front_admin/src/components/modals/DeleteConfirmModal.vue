<template>
	<div v-if="isOpen" class="modal-overlay" @click="close">
		<div class="modal-content" @click.stop>
			<div class="modal-header">
				<h5 class="modal-title">{{ title }}</h5>
				<button type="button" class="btn-close" @click="close" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>{{ message }}</p>
				<div v-if="itemName" class="alert alert-warning">
					<strong>{{ itemName }}</strong>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" @click="close" :disabled="isDeleting">
					{{ cancelText }}
				</button>
				<button type="button" class="btn btn-danger" @click="confirm" :disabled="isDeleting">
					<span v-if="isDeleting" class="spinner-border spinner-border-sm me-2" role="status"></span>
					{{ confirmText }}
				</button>
			</div>
		</div>
	</div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
	isOpen: {
		type: Boolean,
		default: false
	},
	title: {
		type: String,
		default: 'Confirm Deletion'
	},
	message: {
		type: String,
		default: 'Are you sure you want to delete this item?'
	},
	itemName: {
		type: String,
		default: ''
	},
	confirmText: {
		type: String,
		default: 'Delete'
	},
	cancelText: {
		type: String,
		default: 'Cancel'
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

const confirm = () => {
	emit('confirm')
}
</script>

<style scoped>
.modal-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;
	z-index: 1050;
}

.modal-content {
	background: white;
	border-radius: 0.375rem;
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
	max-width: 500px;
	width: 90%;
	max-height: 90vh;
	overflow-y: auto;
}

.modal-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 1rem 1.5rem;
	border-bottom: 1px solid #dee2e6;
}

.modal-title {
	margin: 0;
	font-size: 1.25rem;
	font-weight: 500;
}

.btn-close {
	background: none;
	border: none;
	font-size: 1.5rem;
	cursor: pointer;
	opacity: 0.5;
	padding: 0;
	width: 1em;
	height: 1em;
	display: flex;
	align-items: center;
	justify-content: center;
}

.btn-close:hover {
	opacity: 0.75;
}

.modal-body {
	padding: 1rem 1.5rem;
}

.modal-footer {
	display: flex;
	justify-content: flex-end;
	gap: 0.5rem;
	padding: 1rem 1.5rem;
	border-top: 1px solid #dee2e6;
}

.btn {
	padding: 0.375rem 0.75rem;
	border: 1px solid transparent;
	border-radius: 0.375rem;
	cursor: pointer;
	font-size: 1rem;
	line-height: 1.5;
	text-align: center;
	text-decoration: none;
	transition: all 0.15s ease-in-out;
}

.btn-secondary {
	color: #6c757d;
	background-color: #f8f9fa;
	border-color: #6c757d;
}

.btn-secondary:hover {
	color: #545b62;
	background-color: #e2e6ea;
	border-color: #545b62;
}

.btn-danger {
	color: #fff;
	background-color: #dc3545;
	border-color: #dc3545;
}

.btn-danger:hover {
	color: #fff;
	background-color: #c82333;
	border-color: #bd2130;
}

.btn:disabled {
	opacity: 0.65;
	cursor: not-allowed;
}

.alert {
	padding: 0.75rem 1rem;
	margin-bottom: 1rem;
	border: 1px solid transparent;
	border-radius: 0.375rem;
}

.alert-warning {
	color: #856404;
	background-color: #fff3cd;
	border-color: #ffeaa7;
}

.spinner-border {
	display: inline-block;
	width: 1rem;
	height: 1rem;
	border: 0.25em solid currentColor;
	border-right-color: transparent;
	border-radius: 50%;
	animation: spinner-border 0.75s linear infinite;
}

.spinner-border-sm {
	width: 0.8rem;
	height: 0.8rem;
	border-width: 0.2em;
}

@keyframes spinner-border {
	to {
		transform: rotate(360deg);
	}
}
</style>