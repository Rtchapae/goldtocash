<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Messages" :loading="isLoadingChats" />

			<div class="row">
				<div class="col-12">
					<div class="row">
						<!-- Chat List -->
						<div class="col-12 col-lg-4">
							<ChatsList
								:chats="chats"
								:selected-thread-id="selectedThreadId"
								:is-loading="isLoadingChats"
								@select="selectChat"
							/>
						</div>

						<!-- Messages Area -->
						<div class="col-12 col-lg-8">
							<MessagesArea
								ref="messagesAreaRef"
								:selected-thread-id="selectedThreadId"
								:messages="messages"
								:is-loading="isLoadingMessages"
								:is-sending="isSending"
								:user="selectedUser"
								:is-loading-chats="isLoadingChats"
								@send="handleSendMessage"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, onActivated, nextTick, watch } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import ChatsList from '@/components/messages/ChatsList.vue'
import MessagesArea from '@/components/messages/MessagesArea.vue'
import { getChats, getThreadMessages, sendMessage } from '@/api/adminMessages'
import { useToast } from '@/composables/useToast'
import { useCounters } from '@/composables/useCounters'
import { useCountersStore } from '@/stores/counters'

const toast = useToast()
const { loadCounters } = useCounters()
const countersStore = useCountersStore()

const chats = ref([])
const selectedThreadId = ref(null)
const selectedUser = ref(null)
const messages = ref([])
const isLoadingChats = ref(false)
const isLoadingMessages = ref(false)
const isSending = ref(false)
const messagesAreaRef = ref(null)

const loadChats = async () => {
	isLoadingChats.value = true
	try {
		const response = await getChats()
		if (response.status && response.chats) {
			chats.value = response.chats
			updateUnreadCount()
		}
	} catch (error) {
		console.error('Failed to load chats:', error)
		toast.error('Failed to load chats')
	} finally {
		isLoadingChats.value = false
	}
}

const updateUnreadCount = () => {
	const totalUnread = chats.value.reduce((sum, chat) => sum + (chat.unread_count || 0), 0)
	countersStore.updateCounter('newMessages', totalUnread)
}

const selectChat = async (threadId) => {
	selectedThreadId.value = threadId
	loadMessages(threadId)
}

const loadMessages = async (threadId) => {
	if (!threadId) return

	isLoadingMessages.value = true
	try {
		const response = await getThreadMessages(threadId)
		if (response.status) {
			messages.value = response.messages || []
			selectedUser.value = response.user || null
			await loadChats()
		}
		await nextTick()
		setTimeout(() => {
			scrollToBottom()
		}, 150)
	} catch (error) {
		console.error('Failed to load messages:', error)
		toast.error('Failed to load messages')
	} finally {
		isLoadingMessages.value = false
		await nextTick()
		setTimeout(() => {
			scrollToBottom()
		}, 200)
	}
}

const scrollToBottom = () => {
	nextTick(() => {
		if (messagesAreaRef.value) {
			messagesAreaRef.value.scrollToBottom()
		}
	})
}

const handleSendMessage = async (text) => {
	if (!text.trim() || isSending.value || !selectedThreadId.value) return

	const tempMessage = {
		id: `temp_${Date.now()}`,
		text,
		isSent: true,
		isRead: false,
		createdAt: new Date().toISOString(),
	}
	messages.value.push(tempMessage)
	scrollToBottom()

	isSending.value = true
	try {
		const response = await sendMessage(selectedThreadId.value, text)
		if (response.status && response.message) {
			const index = messages.value.findIndex(m => m.id === tempMessage.id)
			if (index >= 0) {
				messages.value[index] = response.message
			}
			scrollToBottom()
			loadChats()
		}
	} catch (error) {
		console.error('Failed to send message:', error)
		toast.error('Failed to send message')
		messages.value = messages.value.filter(m => m.id !== tempMessage.id)
	} finally {
		isSending.value = false
	}
}

watch(() => messages.value.length, () => {
	scrollToBottom()
})

watch(() => selectedThreadId.value, () => {
	if (selectedThreadId.value) {
		setTimeout(() => {
			scrollToBottom()
		}, 300)
	}
})

watch(() => chats.value, () => {
	updateUnreadCount()
}, { deep: true })

onMounted(() => {
	loadChats()
})

onActivated(() => {
	loadChats()
})
</script>

<style scoped>
</style>
