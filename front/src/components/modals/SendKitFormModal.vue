<template>
	<div class="kit-modal-root">
		<div class="kit-modal-overlay" @click="$emit('close')"></div>
		<div class="kit-modal-dialog" role="dialog" aria-modal="true">
			<div class="modal-content">
				<button type="button" class="close" aria-label="Close" @click="$emit('close')">
					<span aria-hidden="true">
						<img src="/images/close-icon.svg" alt="Close">
					</span>
				</button>
				<KitForm />
			</div>
		</div>
	</div>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'
</script>

<style scoped>
.kit-modal-root { position: fixed; inset: 0; z-index: 11000; }
.kit-modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.6); }
.kit-modal-dialog { position: relative; z-index: 1; max-width: 640px; margin: 5vh auto; }
.modal-content { background: #fff; border-radius: 8px; overflow: hidden; }
.close { position: absolute; right: 8px; top: 8px; background: transparent; }
</style>