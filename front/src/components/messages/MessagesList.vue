<template>
	<div class="messages-list" ref="messagesListRef">
		<!-- Loading State -->
		<div v-if="isLoading" class="messages-loading">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>

		<div v-else-if="messages.length === 0" class="text-center p-5 text-muted">
			<p>{{ EMPTY_STATES.NO_MESSAGES }}</p>
		</div>

		<template v-else>
			<div v-if="conversationStartDate" class="conversation-date-divider">
				<span class="conversation-date-text">{{ formatConversationDate(conversationStartDate) }}</span>
			</div>

			<MessageItem
				v-for="message in messages"
				:key="message.id"
				:message="message"
			/>
		</template>
	</div>
</template>

<script setup>
import { computed, ref, watch, nextTick } from 'vue'
import MessageItem from './MessageItem.vue'
import { formatConversationDate } from '@/utils/formatters'
import { EMPTY_STATES } from '@/constants/messages'

const props = defineProps({
	messages: { type: Array, default: () => [] },
	isLoading: { type: Boolean, default: false },
})

const messagesListRef = ref(null)

const conversationStartDate = computed(() => {
	if (props.messages.length === 0) return null
	const dates = props.messages.map(m => new Date(m.createdAt)).filter(d => !isNaN(d.getTime()))
	if (dates.length === 0) return null
	return new Date(Math.min(...dates.map(d => d.getTime())))
})

const scrollToBottom = () => {
	if (messagesListRef.value) {
		messagesListRef.value.scrollTop = messagesListRef.value.scrollHeight
	}
}

watch(() => props.messages, () => {
	nextTick(() => {
		scrollToBottom()
	})
}, { deep: true })

// Expose scroll function for parent
defineExpose({
	scrollToBottom,
})
</script>


