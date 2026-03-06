<template>
	<div
		class="conversation-item"
		:class="{ active: isActive }"
		@click="$emit('click')"
	>
		<div class="conversation-avatar">
			<i :class="`${icon} fa-2x`"></i>
		</div>
		<div class="conversation-info">
			<div class="conversation-name">{{ name }}</div>
			<div class="conversation-preview">{{ preview }}</div>
		</div>
		<div class="conversation-meta">
			<span v-if="unreadCount > 0" class="badge bg-danger">{{ unreadCount }}</span>
		</div>
	</div>
</template>

<script setup>
defineProps({
	conversationId: { type: String, required: true },
	name: { type: String, required: true },
	preview: { type: String, default: '' },
	unreadCount: { type: Number, default: 0 },
	isActive: { type: Boolean, default: false },
	icon: { type: String, default: 'fas fa-user' },
})

defineEmits(['click'])
</script>


