<template>
	<Editor
		:id="editorDomId"
		:key="editorDomId"
		v-model="localHtml"
		:init="editorInit"
		:tinymce-script-src="TINYMCE_SCRIPT_SRC"
	/>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import { resolveAdminAssetUrl } from '@/utils/apiAssetUrl'

const TINYMCE_LICENSE_KEY = 'gpl'
const TINYMCE_SCRIPT_SRC = '/tinymce/tinymce.min.js'
const TINYMCE_BASE_URL = '/tinymce'

const props = defineProps({
	modelValue: {
		type: String,
		default: '',
	},
	height: {
		type: Number,
		default: 280,
	},
	/** Compact toolbar for FAQ answers */
	compact: {
		type: Boolean,
		default: false,
	},
})

const emit = defineEmits(['update:modelValue'])

const localHtml = ref(props.modelValue || '')
const editorDomId = `pb-tiny-${Math.random().toString(36).slice(2, 10)}`

watch(
	() => props.modelValue,
	(v) => {
		if (v !== localHtml.value) localHtml.value = v || ''
	}
)

watch(localHtml, (v) => {
	emit('update:modelValue', v)
})

function tinymceImagePrependUrl() {
	const resolved = resolveAdminAssetUrl('/storage/')
	if (!resolved || !/^https?:\/\//i.test(resolved)) return ''
	try {
		return new URL(resolved).origin
	} catch {
		return ''
	}
}

const toolbar = computed(() =>
	props.compact
		? 'bold italic underline | link | bullist numlist | removeformat'
		: 'undo redo | fontfamily fontsize | bold italic underline strikethrough | forecolor | alignleft aligncenter alignright | bullist numlist | link unlink | removeformat | code'
)

const editorInit = computed(() => ({
	license_key: TINYMCE_LICENSE_KEY,
	base_url: TINYMCE_BASE_URL,
	suffix: '.min',
	height: props.height,
	menubar: false,
	branding: false,
	plugins: 'link lists code',
	toolbar: toolbar.value,
	font_family_formats:
		'Montserrat=Montserrat,sans-serif;Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino,serif;Times New Roman=times new roman,times,serif;Courier New=courier new,courier,monospace;Verdana=verdana,geneva,sans-serif',
	font_size_formats: '12px 14px 16px 18px 20px 24px 28px 32px',
	content_style:
		'body { font-family: Montserrat, sans-serif; font-size: 15px; line-height: 1.55; } a { color: #c39e3d; }',
	link_default_target: '_blank',
	link_assume_external_targets: true,
	convert_urls: false,
	relative_urls: false,
	remove_script_host: false,
	image_prepend_url: tinymceImagePrependUrl(),
	skin: 'oxide',
	content_css: false,
	setup: (editor) => {
		editor.on('change keyup', () => {
			localHtml.value = editor.getContent()
		})
	},
}))
</script>
