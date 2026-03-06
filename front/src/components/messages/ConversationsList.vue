<template>
	<div class="card p-2">
		<div class="conversations-list">
			<ConversationItem
				:conversation-id="CONVERSATION_IDS.SUPPORT"
				:name="SUPPORT_LABELS.NAME"
				:preview="supportPreview || SUPPORT_LABELS.EMPTY_PREVIEW"
				:unread-count="supportUnreadCount"
				:is-active="selectedConversationId === CONVERSATION_IDS.SUPPORT"
				icon="fas fa-headset"
				@click="$emit('select', CONVERSATION_IDS.SUPPORT)"
			/>

			<ConversationItem
				v-for="conv in conversations"
				:key="conv.id"
				:conversation-id="conv.id"
				:name="conv.name"
				:preview="conv.lastMessage || NO_MESSAGES_PREVIEW"
				:unread-count="conv.unreadCount"
				:is-active="selectedConversationId === conv.id"
				icon="fas fa-user"
				@click="$emit('select', conv.id)"
			/>

			<div v-if="conversations.length === 0 && !selectedConversationId" class="text-center p-3 text-muted">
				{{ EMPTY_STATES.NO_OTHER_CONVERSATIONS }}
			</div>
		</div>
	</div>
</template>

<script setup>
import ConversationItem from './ConversationItem.vue'
import { CONVERSATION_IDS, SUPPORT_LABELS, EMPTY_STATES } from '@/constants/messages'

const { NO_MESSAGES_PREVIEW } = EMPTY_STATES

defineProps({
	selectedConversationId: { type: String, required: true },
	conversations: { type: Array, default: () => [] },
	supportPreview: { type: String, default: '' },
	supportUnreadCount: { type: Number, default: 0 },
})

defineEmits(['select'])
</script>

