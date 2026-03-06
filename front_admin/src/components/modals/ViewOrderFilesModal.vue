<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="viewOrderFilesLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document" style="max-width: 800px !important;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewOrderFilesLabel">View Order Files</h5>
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
							<tr v-if="hasFiles">
								<td>
									<div class="d-flex flex-wrap">
										<div
											v-for="file in files"
											:key="file.id || file.type"
											class="m-2 text-center file-card d-flex flex-column align-items-center"
										>
											<i class="far fa-file-image fa-3x"></i>
											<button
												v-if="file.apiPath"
												type="button"
												class="btn btn-sm btn-block btn-primary mb-3"
												@click="openFile(file)"
											>
												View
											</button>
										</div>
									</div>
								</td>
							</tr>
							<tr v-else>
								<td>
									No Order Files Available
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-primary btn-fw"
						@click="close"
					>
						Close
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
import { computed } from 'vue'
import { downloadOrderFile } from '@/api/adminOrders'
import { useToast } from '@/composables/useToast'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	files: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits(['close'])

const toast = useToast()

const close = () => {
	emit('close')
}

const openFile = async (file) => {
	try {
		const blob = await downloadOrderFile(file.apiPath)
		const url = window.URL.createObjectURL(blob)
		const link = document.createElement('a')
		link.href = url
		link.target = '_blank'
		link.rel = 'noopener'
		link.style.display = 'none'
		document.body.appendChild(link)
		link.click()
		document.body.removeChild(link)
		window.URL.revokeObjectURL(url)
	} catch (error) {
		console.error('Failed to open file:', error)
		toast.error('Failed to open file')
	}
}

const hasFiles = computed(() => Array.isArray(props.files) && props.files.length > 0)
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


