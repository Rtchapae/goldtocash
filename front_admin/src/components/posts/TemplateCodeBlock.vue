<template>
	<div class="position-relative">
		<pre class="template-code" :data-element="elementId">
			<code>{{ code }}</code>
		</pre>
		<button
			type="button"
			class="btn btn-sm btn-outline-secondary copy-code-btn"
			:title="copied ? 'Copied!' : 'Copy to clipboard'"
			:class="{ 'btn-success': copied }"
			@click="handleCopy"
		>
			<i :class="['fa', copied ? 'fa-check' : 'fa-copy']"></i>
			{{ copied ? 'Copied!' : 'Copy' }}
		</button>
	</div>
</template>

<script setup>
import { ref } from 'vue'
import { useClipboard } from '@/composables/useClipboard'
import { useToast } from '@/composables/useToast'

const props = defineProps({
	code: {
		type: String,
		required: true
	},
	elementId: {
		type: String,
		required: true
	}
})

const { copyToClipboard } = useClipboard()
const { showToast } = useToast()
const copied = ref(false)

const handleCopy = async () => {
	const success = await copyToClipboard(props.code)
	if (success) {
		showToast('Copied to clipboard', 'success')
		copied.value = true
		setTimeout(() => {
			copied.value = false
		}, 2000)
	} else {
		showToast('Failed to copy to clipboard', 'error')
	}
}
</script>
