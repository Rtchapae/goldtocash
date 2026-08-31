<template>
	<div class="kit-modal-root">
		<div class="kit-modal-overlay" @click="$emit('close')"></div>
		<div class="kit-modal-dialog" role="dialog" aria-modal="true">
			<div class="kit-modal-content">
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
.kit-modal-root { position: fixed; inset: 0; z-index: 11000; display: flex; align-items: flex-start; justify-content: center; overflow-y: auto; padding: 1vh 16px; }
.kit-modal-overlay { position: fixed; inset: 0; z-index: 0; background: rgba(0,0,0,.6); }
.kit-modal-dialog { position: relative; z-index: 1; max-width: 640px; width: 100%; margin: 0 auto; flex-shrink: 0; }
.kit-modal-content { background: #fff; border-radius: 8px; overflow-y: auto; max-height: min(calc(98vh - 8px), 1400px); padding-top: 24px; }
.close { position: absolute; right: 8px; top: 8px; background: transparent; z-index: 1; }
</style>