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
					class="btn btn-primary"
					:disabled="!messageText.trim() || isSending"
				>
					<span v-if="isSending" class="spinner-border spinner-border-sm me-2" role="status"></span>
					<i v-else class="fas fa-paper-plane me-2"></i>
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


