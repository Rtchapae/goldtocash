<template>
	<div v-if="selectedConversationId" class="card messages-container">
		<MessagesHeader />

		<MessagesList
			ref="messagesListRef"
			:messages="messages"
			:is-loading="isLoading"
		/>

		<MessageInput
			:is-sending="isSending"
			@send="$emit('send', $event)"
		/>
	</div>

	<EmptyState v-else />
</template>

<script setup>
import { ref } from 'vue'
import MessagesHeader from './MessagesHeader.vue'
import MessagesList from './MessagesList.vue'
import MessageInput from './MessageInput.vue'
import EmptyState from './EmptyState.vue'

defineProps({
	selectedConversationId: { type: String, default: null },
	messages: { type: Array, default: () => [] },
	isLoading: { type: Boolean, default: false },
	isSending: { type: Boolean, default: false },
})

defineEmits(['send'])

const messagesListRef = ref(null)

const scrollToBottom = () => {
	if (messagesListRef.value) {
		messagesListRef.value.scrollToBottom()
	}
}

defineExpose({
	scrollToBottom,
})
</script>

