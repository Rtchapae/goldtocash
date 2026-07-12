<template>
	<div class="lp-block-card" :class="{ 'lp-block-card--collapsed': collapsed }">
		<div class="lp-block-card__header">
			<button type="button" class="lp-block-card__toggle" @click="collapsed = !collapsed">
				<span class="lp-block-card__type">{{ blockLabel }}</span>
				<i :class="collapsed ? 'fas fa-chevron-down' : 'fas fa-chevron-up'"></i>
			</button>
			<div class="lp-block-card__actions">
				<button type="button" class="btn btn-sm btn-outline-secondary" :disabled="!canMoveUp" title="Move up" @click="$emit('move-up')">
					<i class="fas fa-arrow-up"></i>
				</button>
				<button type="button" class="btn btn-sm btn-outline-secondary" :disabled="!canMoveDown" title="Move down" @click="$emit('move-down')">
					<i class="fas fa-arrow-down"></i>
				</button>
				<button type="button" class="btn btn-sm btn-outline-danger" title="Delete" @click="$emit('remove')">
					<i class="fas fa-trash"></i>
				</button>
			</div>
		</div>

		<div v-show="!collapsed" class="lp-block-card__body">
			<template v-if="block.type === 'heading'">
				<div class="mb-3">
					<label class="form-label">Heading text</label>
					<input v-model="data.text" type="text" class="form-control" />
				</div>
				<div class="row g-2">
					<div class="col-6 col-md-4">
						<label class="form-label">Size</label>
						<select v-model.number="data.level" class="form-select">
							<option :value="1">H1</option>
							<option :value="2">H2</option>
							<option :value="3">H3</option>
						</select>
					</div>
					<div class="col-6 col-md-4">
						<label class="form-label">Align</label>
						<select v-model="data.align" class="form-select">
							<option v-for="opt in ALIGN_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
						</select>
					</div>
					<div class="col-12 col-md-4">
						<label class="form-label">Color (optional)</label>
						<input v-model="data.color" type="text" class="form-control" placeholder="#333333" />
					</div>
				</div>
			</template>

			<template v-else-if="block.type === 'text'">
				<div class="row g-2 mb-3">
					<div class="col-6">
						<label class="form-label">Font size</label>
						<select v-model="data.fontSize" class="form-select">
							<option v-for="opt in FONT_SIZE_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
						</select>
					</div>
					<div class="col-6">
						<label class="form-label">Align</label>
						<select v-model="data.align" class="form-select">
							<option v-for="opt in ALIGN_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
						</select>
					</div>
				</div>
				<label class="form-label">Content (HTML allowed)</label>
				<SimpleRichText v-model="data.content" :rows="6" />
			</template>

			<template v-else-if="block.type === 'image'">
				<ImageUploadField v-model="data.src" />
				<div class="row g-2 mt-2">
					<div class="col-12">
						<label class="form-label">Alt text</label>
						<input v-model="data.alt" type="text" class="form-control" />
					</div>
					<div class="col-12">
						<label class="form-label">Caption</label>
						<input v-model="data.caption" type="text" class="form-control" />
					</div>
					<div class="col-6">
						<label class="form-label">Width</label>
						<select v-model="data.width" class="form-select">
							<option value="full">Full width</option>
							<option value="half">Half width</option>
						</select>
					</div>
					<div class="col-6">
						<label class="form-label">Align</label>
						<select v-model="data.align" class="form-select">
							<option v-for="opt in ALIGN_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
						</select>
					</div>
				</div>
			</template>

			<template v-else-if="block.type === 'text_image'">
				<div class="mb-3">
					<label class="form-label">Section title</label>
					<input v-model="data.title" type="text" class="form-control" />
				</div>
				<label class="form-label">Text</label>
				<SimpleRichText v-model="data.text" :rows="5" class="mb-3" />
				<ImageUploadField v-model="data.image" />
				<div class="mt-2">
					<label class="form-label">Image position</label>
					<select v-model="data.imagePosition" class="form-select">
						<option value="left">Image left</option>
						<option value="right">Image right</option>
					</select>
				</div>
			</template>

			<template v-else-if="block.type === 'cta'">
				<div class="mb-3">
					<label class="form-label">Button text</label>
					<input v-model="data.text" type="text" class="form-control" />
				</div>
				<div class="mb-3">
					<label class="form-label">Link URL</label>
					<input v-model="data.url" type="text" class="form-control" placeholder="/contact-us" />
				</div>
				<div class="row g-2">
					<div class="col-6">
						<label class="form-label">Style</label>
						<select v-model="data.style" class="form-select">
							<option value="primary">Primary (gold)</option>
							<option value="secondary">Secondary</option>
						</select>
					</div>
					<div class="col-6">
						<label class="form-label">Align</label>
						<select v-model="data.align" class="form-select">
							<option v-for="opt in ALIGN_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
						</select>
					</div>
				</div>
			</template>

			<template v-else-if="block.type === 'spacer'">
				<label class="form-label">Height</label>
				<select v-model="data.height" class="form-select">
					<option v-for="opt in SPACER_HEIGHT_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
				</select>
			</template>

			<template v-else-if="block.type === 'divider'">
				<label class="form-label">Style</label>
				<select v-model="data.style" class="form-select">
					<option value="line">Line</option>
					<option value="dots">Dots</option>
				</select>
			</template>

			<div class="lp-block-card__preview mt-3">
				<div class="small text-muted mb-1">Preview</div>
				<BlockPreview :block="previewBlock" />
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { BLOCK_TYPES, ALIGN_OPTIONS, FONT_SIZE_OPTIONS, SPACER_HEIGHT_OPTIONS } from '@/constants/landingPageBlocks'
import BlockPreview from './BlockPreview.vue'
import SimpleRichText from './SimpleRichText.vue'
import ImageUploadField from './ImageUploadField.vue'

const props = defineProps({
	block: { type: Object, required: true },
	canMoveUp: { type: Boolean, default: false },
	canMoveDown: { type: Boolean, default: false },
})

defineEmits(['move-up', 'move-down', 'remove'])

const collapsed = ref(false)
const data = computed(() => props.block.data)

const blockLabel = computed(() => {
	return BLOCK_TYPES.find((b) => b.type === props.block.type)?.label || props.block.type
})

const previewBlock = computed(() => props.block)
</script>

<style scoped>
.lp-block-card {
	border: 1px solid #dee2e6;
	border-radius: 0.5rem;
	margin-bottom: 0.75rem;
	background: #fff;
}

.lp-block-card__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 0.5rem;
	padding: 0.5rem 0.75rem;
	background: #f8f9fa;
	border-bottom: 1px solid #eee;
}

.lp-block-card__toggle {
	flex: 1;
	display: flex;
	align-items: center;
	justify-content: space-between;
	border: 0;
	background: transparent;
	padding: 0.5rem 0;
	min-height: 2.75rem;
	text-align: left;
	font-weight: 600;
}

.lp-block-card__actions {
	display: flex;
	gap: 0.35rem;
}

.lp-block-card__actions .btn {
	min-width: 2.5rem;
	min-height: 2.5rem;
	padding: 0.35rem;
}

.lp-block-card__body {
	padding: 0.75rem;
}

.lp-block-card__preview {
	border-top: 1px dashed #dee2e6;
	padding-top: 0.75rem;
}
</style>
