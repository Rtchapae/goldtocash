<template>
	<section class="section-user_account content">
		<div class="m-3">
			<div class="row">
				<div class="col-12">
					<div class="card p-3 mb-3">
						<h4 class="mb-0">Messages</h4>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12 col-lg-4">
					<ConversationsList
						:selected-conversation-id="selectedConversationId"
						:conversations="conversations"
						:support-preview="supportPreview"
						:support-unread-count="supportUnreadCount"
						@select="selectConversation"
					/>
				</div>

				<div class="col-12 col-lg-8">
					<MessagesArea
						ref="messagesAreaRef"
						:selected-conversation-id="selectedConversationId"
						:messages="currentMessages"
						:is-loading="isLoadingMessages"
						:is-sending="isSending"
						@send="handleSendMessage"
					/>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useToast } from '@/composables/useToast'
import { useUserStore } from '@/stores/user'
import { getMessages, sendMessage as apiSendMessage, markMessagesAsRead } from '@/api/userAccount'
import { CONVERSATION_IDS, SUPPORT_LABELS, MESSAGE_PREFIXES } from '@/constants/messages'
import ConversationsList from '@/components/messages/ConversationsList.vue'
import MessagesArea from '@/components/messages/MessagesArea.vue'

const toast = useToast()
const userStore = useUserStore()

const selectedConversationId = ref(CONVERSATION_IDS.SUPPORT)
const conversations = ref([])
const messages = ref({})
const isSending = ref(false)
const isLoadingMessages = ref(false)

const supportPreview = computed(() => {
	const supportMessages = messages.value[CONVERSATION_IDS.SUPPORT] || []
	if (supportMessages.length === 0) return SUPPORT_LABELS.EMPTY_PREVIEW
	const lastMessage = supportMessages[supportMessages.length - 1]
	return lastMessage.text
})

const supportUnreadCount = computed(() => {
	const supportMessages = messages.value[CONVERSATION_IDS.SUPPORT] || []
	// Count unread messages from admin (isSent: false means message from admin)
	return supportMessages.filter(m => !m.isRead && !m.isSent).length
})

// Watch for changes in unread count and update store
watch(supportUnreadCount, (newCount) => {
	userStore.setUnreadMessagesCount(newCount)
}, { immediate: true })

const currentMessages = computed(() => {
	return messages.value[selectedConversationId.value] || []
})

const messagesAreaRef = ref(null)

const selectConversation = async (conversationId) => {
	selectedConversationId.value = conversationId
	
	// Mark messages as read on server
	if (conversationId === CONVERSATION_IDS.SUPPORT) {
		try {
			await markMessagesAsRead()
			// Update local state
			if (messages.value[conversationId]) {
				messages.value[conversationId].forEach(msg => {
					if (!msg.isSent) {
						msg.isRead = true
					}
				})
			}
		} catch (error) {
			console.error('Failed to mark messages as read:', error)
		}
	}
	
	// Scroll to bottom
	nextTick(() => {
		scrollToBottom()
	})
}

const scrollToBottom = () => {
	if (messagesAreaRef.value) {
		messagesAreaRef.value.scrollToBottom()
	}
}

const handleSendMessage = async (text) => {
	if (!text.trim() || isSending.value) return

	// Add message to local state immediately (optimistic update)
	const conversationId = selectedConversationId.value
	if (!messages.value[conversationId]) {
		messages.value[conversationId] = []
	}

	const tempMessage = {
		id: `${MESSAGE_PREFIXES.TEMP}${Date.now()}`,
		text,
		isSent: true,
		isRead: false,
		createdAt: new Date().toISOString(),
	}

	messages.value[conversationId].push(tempMessage)

	isSending.value = true

	try {
		// Send message via API
		const response = await apiSendMessage(text)
		
		if (!response.status || !response.message) {
			throw new Error('Invalid response from server')
		}

		// Replace temp message with real message from server
		// Temp message is always the last one we just added
		const lastIndex = messages.value[conversationId].length - 1
		if (lastIndex >= 0 && messages.value[conversationId][lastIndex].id === tempMessage.id) {
			messages.value[conversationId][lastIndex] = response.message
		}
		
		// Scroll to bottom after message is added
		nextTick(() => {
			scrollToBottom()
		})

		toast.success('Message sent successfully!')
	} catch (error) {
		console.error('Failed to send message:', error)
		toast.error(error?.data?.message || error?.message || 'Failed to send message. Please try again.')
		
		// Remove temp message on error
		messages.value[conversationId] = messages.value[conversationId].filter(m => m.id !== tempMessage.id)
	} finally {
		isSending.value = false
	}
}

const loadMessages = async () => {
	isLoadingMessages.value = true
	try {
		const response = await getMessages()
		
		if (response.status && response.messages) {
			// Store messages directly (no mapping - backend returns final format)
			messages.value[CONVERSATION_IDS.SUPPORT] = response.messages
		} else {
			// Initialize empty support conversation if no messages
			if (!messages.value[CONVERSATION_IDS.SUPPORT]) {
				messages.value[CONVERSATION_IDS.SUPPORT] = []
			}
		}

		// Scroll to bottom after loading
		nextTick(() => {
			scrollToBottom()
		})
	} catch (error) {
		console.error('Failed to load messages:', error)
		toast.error(error?.data?.message || error?.message || 'Failed to load messages. Please try again.')
		
		// Initialize empty support conversation on error
		if (!messages.value[CONVERSATION_IDS.SUPPORT]) {
			messages.value[CONVERSATION_IDS.SUPPORT] = []
		}
	} finally {
		isLoadingMessages.value = false
	}
}


onMounted(() => {
	loadMessages()
	// Auto-select support conversation
	selectConversation(CONVERSATION_IDS.SUPPORT)
})
</script>

<style scoped>
/* Styles moved to front/src/styles/partials/_messages.scss */
</style>

