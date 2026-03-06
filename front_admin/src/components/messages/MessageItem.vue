<template>
	<div
		class="message-item"
		:class="{ 'message-sent': message.isSent, 'message-received': !message.isSent }"
	>
		<div class="message-content">
			<div class="message-text">{{ message.text }}</div>
			<div class="message-time">{{ formatMessageTime(message.createdAt) }}</div>
		</div>
	</div>
</template>

<script setup>
import { formatMessageTime } from '@/utils/formatters'

defineProps({
	message: { type: Object, required: true },
})
</script>

