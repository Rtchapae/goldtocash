<template>
	<div class="messages-header p-3 border-bottom">
		<div class="d-flex align-items-center">
			<div class="conversation-avatar me-3">
				<iconify-icon icon="solar:user-outline" class="icon conversation-avatar-icon"></iconify-icon>
			</div>
			<div>
				<h5 class="mb-0">{{ user?.name || 'Support Chat' }}</h5>
				<small class="text-muted">{{ user?.email || '' }}</small>
			</div>
		</div>
	</div>
</template>

<script setup>
defineProps({
	user: { type: Object, default: null },
})
</script>

