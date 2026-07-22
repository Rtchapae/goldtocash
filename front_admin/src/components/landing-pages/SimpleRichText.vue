<template>
	<div class="simple-rich-text">
		<div class="simple-rich-text__toolbar" role="toolbar" aria-label="Text formatting">
			<button type="button" class="btn btn-sm btn-outline-secondary" @click="wrap('strong')" title="Bold">
				<strong>B</strong>
			</button>
			<button type="button" class="btn btn-sm btn-outline-secondary" @click="wrap('em')" title="Italic">
				<em>I</em>
			</button>
			<button type="button" class="btn btn-sm btn-outline-secondary" @click="wrap('u')" title="Underline">
				<u>U</u>
			</button>
			<button type="button" class="btn btn-sm btn-outline-secondary" @click="insertList" title="List">
				• List
			</button>
			<button type="button" class="btn btn-sm btn-outline-secondary" @click="insertLink" title="Link">
				Link
			</button>
		</div>
		<textarea
			ref="textareaRef"
			class="form-control simple-rich-text__area"
			:value="modelValue"
			:rows="rows"
			:placeholder="placeholder"
			@input="onInput"
		/>
	</div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
	modelValue: { type: String, default: '' },
	rows: { type: Number, default: 5 },
	placeholder: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const textareaRef = ref(null)

const onInput = (event) => {
	emit('update:modelValue', event.target.value)
}

const wrapSelection = (before, after = before) => {
	const el = textareaRef.value
	if (!el) return
	const start = el.selectionStart
	const end = el.selectionEnd
	const text = el.value
	const selected = text.slice(start, end) || 'text'
	const next = text.slice(0, start) + before + selected + after + text.slice(end)
	emit('update:modelValue', next)
}

const wrap = (tag) => wrapSelection(`<${tag}>`, `</${tag}>`)

const insertList = () => {
	const el = textareaRef.value
	if (!el) return
	const start = el.selectionStart
	const text = el.value
	const insertion = '<ul><li>Item</li></ul>'
	const next = text.slice(0, start) + insertion + text.slice(start)
	emit('update:modelValue', next)
}

const insertLink = () => {
	const url = window.prompt('Link URL', 'https://')
	if (!url) return
	wrapSelection(`<a href="${url}">`, '</a>')
}
</script>

<style scoped>
.simple-rich-text__toolbar {
	display: flex;
	flex-wrap: wrap;
	gap: 0.35rem;
	margin-bottom: 0.5rem;
}

.simple-rich-text__toolbar .btn {
	min-width: 2.5rem;
	min-height: 2.5rem;
}

.simple-rich-text__area {
	font-family: inherit;
	min-height: 6rem;
}
</style>
