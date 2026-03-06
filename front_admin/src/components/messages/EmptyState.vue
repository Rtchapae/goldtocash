<template>
	<div class="card messages-container">
		<div v-if="isLoading" class="messages-loading">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
		<div v-else class="p-5 text-center empty-state-content">
			<iconify-icon icon="solar:chat-round-outline" class="icon text-muted mb-3 empty-state-icon"></iconify-icon>
			<h5>Select a chat</h5>
			<p class="text-muted">Select a chat from the list to view messages</p>
		</div>
	</div>
</template>

<script setup>
defineProps({
	isLoading: { type: Boolean, default: false },
})
</script>

