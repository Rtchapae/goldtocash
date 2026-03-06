<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="viewUserFilesLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document" style="max-width: 800px !important;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewUserFilesLabel">View User Files</h5>
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
											class="m-2 text-center file-card"
										>
											<i class="far fa-file-image fa-3x"></i>
											<form
												v-if="file.url"
												method="get"
												target="_blank"
												class="mb-3"
												:action="file.url"
											>
												<input
													type="submit"
													class="btn btn-sm btn-block btn-primary"
													value="View"
												>
											</form>
										</div>
									</div>
								</td>
							</tr>
							<tr v-else>
								<td>
									No User Files Available
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

const close = () => {
	emit('close')
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





