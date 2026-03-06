<template>
	<div class="messages-input p-3 border-top">
		<form @submit.prevent="handleSubmit">
			<div class="input-group">
				<input
					v-model="messageText"
					type="text"
					class="form-control"
					placeholder="Type your message..."
					:disabled="isSending"
				>
				<button
					type="submit"
					class="btn btn-primary message-send-btn"
					:disabled="!messageText.trim() || isSending"
				>
					<span v-if="isSending" class="spinner-border spinner-border-sm me-2" role="status"></span>
					<iconify-icon v-else icon="solar:plain-2-outline" class="icon me-2"></iconify-icon>
					Send
				</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
	isSending: { type: Boolean, default: false },
})

const emit = defineEmits(['send'])

const messageText = ref('')

const handleSubmit = () => {
	if (!messageText.value.trim() || props.isSending) return
	emit('send', messageText.value.trim())
	messageText.value = ''
}
</script>

