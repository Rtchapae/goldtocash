<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="editOrderShippingDetailsLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="handleSubmit">
					<div class="modal-header">
						<h5 class="modal-title" id="editOrderShippingDetailsLabel">Edit Order Shipping Details</h5>
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
						<table class="table table-striped">
							<tbody>
								<tr>
									<td class="pt-3">Shipping $ :</td>
									<td>
										<input
											v-model="form.shipping"
											type="number"
											step="any"
											min="0"
											class="form-control"
										>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary btn-fw"
							@click="close"
							:disabled="loading"
						>
							Cancel
						</button>
						<button
							type="submit"
							class="btn btn-primary btn-fw"
							:disabled="loading"
						>
							<span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
							{{ loading ? 'Saving...' : 'Save' }}
						</button>
					</div>
				</form>
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
import { reactive, watch } from 'vue'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	order: {
		type: Object,
		default: null
	},
	loading: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
	shipping: 0.00
})

watch(
	() => props.order,
	(order) => {
		if (!order) return
		form.shipping = order.shipping ? parseFloat(order.shipping) : 0.00
	},
	{ immediate: true }
)

const close = () => {
	emit('close')
}

const handleSubmit = () => {
	emit('save', { ...form })
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


