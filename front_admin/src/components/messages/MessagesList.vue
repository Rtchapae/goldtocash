<template>
	<div class="messages-list" ref="messagesListRef">
		<div v-if="isLoading" class="messages-loading">
			<div class="loading-dots">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>

		<div v-else-if="messages.length === 0" class="text-center p-5 text-muted">
			<p>No messages yet</p>
		</div>

		<template v-else>
			<template v-for="(group, groupIndex) in groupedMessages" :key="`date-${groupIndex}-${group.dateKey}`">
				<div class="conversation-date-divider">
					<span class="conversation-date-text">{{ formatDate(group.date) }}</span>
				</div>

				<MessageItem
					v-for="message in group.messages"
					:key="message.id"
					:message="message"
				/>
			</template>
		</template>
	</div>
</template>

<script setup>
import { computed, ref, watch, nextTick, onMounted } from 'vue'
import MessageItem from './MessageItem.vue'
import { formatConversationDate } from '@/utils/formatters'

const props = defineProps({
	messages: { type: Array, default: () => [] },
	isLoading: { type: Boolean, default: false },
})

const messagesListRef = ref(null)

const groupedMessages = computed(() => {
	if (props.messages.length === 0) return []

	const sortedMessages = [...props.messages].sort((a, b) => {
		const dateA = new Date(a.createdAt).getTime()
		const dateB = new Date(b.createdAt).getTime()
		return dateA - dateB
	})

	const groups = []
	let currentDateKey = null
	let currentGroup = null

	for (const message of sortedMessages) {
		const messageDate = new Date(message.createdAt)
		if (isNaN(messageDate.getTime())) continue

		const year = messageDate.getFullYear()
		const month = String(messageDate.getMonth() + 1).padStart(2, '0')
		const day = String(messageDate.getDate()).padStart(2, '0')
		const dateKey = `${year}-${month}-${day}`

		if (dateKey !== currentDateKey) {
			currentDateKey = dateKey
			const groupDate = new Date(year, messageDate.getMonth(), day)
			currentGroup = {
				date: groupDate,
				dateKey: dateKey,
				messages: [],
			}
			groups.push(currentGroup)
		}

		currentGroup.messages.push(message)
	}

	return groups
})

const formatDate = (date) => {
	return formatConversationDate(date)
}

const scrollToBottom = () => {
	if (messagesListRef.value) {
		requestAnimationFrame(() => {
			if (messagesListRef.value) {
				messagesListRef.value.scrollTop = messagesListRef.value.scrollHeight
			}
		})
	}
}

watch(() => props.messages, () => {
	nextTick(() => {
		scrollToBottom()
	})
}, { deep: true })

watch(() => props.isLoading, (newVal, oldVal) => {
	if (oldVal === true && newVal === false) {
		nextTick(() => {
			setTimeout(() => {
				scrollToBottom()
			}, 100)
		})
	}
})

onMounted(() => {
	if (props.messages.length > 0 && !props.isLoading) {
		nextTick(() => {
			setTimeout(() => {
				scrollToBottom()
			}, 100)
		})
	}
})

defineExpose({
	scrollToBottom,
})
</script>

