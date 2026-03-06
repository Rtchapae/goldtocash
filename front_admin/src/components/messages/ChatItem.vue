<template>
	<div
		class="conversation-item"
		:class="{ active: isActive }"
		@click="$emit('click')"
	>
		<div class="conversation-avatar">
			<iconify-icon icon="solar:user-outline" class="icon conversation-avatar-icon"></iconify-icon>
		</div>
		<div class="conversation-info">
			<div class="conversation-name">{{ name }}</div>
			<div class="conversation-preview">{{ preview }}</div>
		</div>
		<div class="conversation-meta">
			<span v-if="unreadCount > 0" class="badge bg-warning text-dark">{{ unreadCount }}</span>
		</div>
	</div>
</template>

<script setup>
defineProps({
	chatId: { type: [String, Number], required: true },
	name: { type: String, required: true },
	preview: { type: String, default: '' },
	unreadCount: { type: Number, default: 0 },
	isActive: { type: Boolean, default: false },
})

defineEmits(['click'])
</script>

