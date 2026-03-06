<template>
	<div class="card p-0">
		<!-- Search Panel -->
		<div class="p-3 border-bottom">
			<div class="input-group">
				<span class="input-group-text">
					<iconify-icon icon="solar:magnifer-outline" class="icon"></iconify-icon>
				</span>
				<input
					v-model="searchQuery"
					type="text"
					class="form-control"
					placeholder="Search chats..."
				/>
				<button
					v-if="searchQuery"
					type="button"
					class="btn btn-outline-secondary"
					@click="clearSearch"
				>
					<iconify-icon icon="solar:close-circle-outline" class="icon"></iconify-icon>
				</button>
			</div>
		</div>

		<!-- Chats List -->
		<div class="conversations-list">
			<div v-if="filteredChats.length === 0 && !isLoading" class="text-center p-3 text-muted">
				<p v-if="searchQuery">No chats found</p>
				<p v-else>No support chats available</p>
			</div>
			<ChatItem
				v-for="chat in filteredChats"
				:key="chat.thread_id"
				:chat-id="chat.thread_id"
				:name="chat.user_name"
				:preview="chat.last_message?.text || 'No messages'"
				:unread-count="chat.unread_count"
				:is-active="selectedThreadId === chat.thread_id"
				@click="$emit('select', chat.thread_id)"
			/>
		</div>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ChatItem from './ChatItem.vue'

const props = defineProps({
	chats: { type: Array, default: () => [] },
	selectedThreadId: { type: [String, Number], default: null },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['select'])

const searchQuery = ref('')

const filteredChats = computed(() => {
	let chats = props.chats

	if (searchQuery.value.trim()) {
		const query = searchQuery.value.toLowerCase().trim()
		chats = chats.filter(chat => {
			const name = (chat.user_name || '').toLowerCase()
			const email = (chat.user_email || '').toLowerCase()
			const preview = (chat.last_message?.text || '').toLowerCase()
			return name.includes(query) || email.includes(query) || preview.includes(query)
		})
	}

	return chats.sort((a, b) => {
		if (a.unread_count > 0 && b.unread_count === 0) return -1
		if (a.unread_count === 0 && b.unread_count > 0) return 1
		
		const dateA = a.last_message?.created_at || a.updated_at || ''
		const dateB = b.last_message?.created_at || b.updated_at || ''
		return dateB.localeCompare(dateA)
	})
})

const clearSearch = () => {
	searchQuery.value = ''
}
</script>
