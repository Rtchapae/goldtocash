<template>
	<div class="lp-block-editor">
		<div class="lp-block-editor__toolbar">
			<div class="dropdown">
				<button
					class="btn btn-primary dropdown-toggle"
					type="button"
					data-bs-toggle="dropdown"
					aria-expanded="false"
				>
					+ Add block
				</button>
				<ul class="dropdown-menu">
					<li v-for="blockType in BLOCK_TYPES" :key="blockType.type">
						<button type="button" class="dropdown-item" @click="addBlock(blockType.type)">
							{{ blockType.label }}
						</button>
					</li>
				</ul>
			</div>
			<button type="button" class="btn btn-outline-secondary" @click="showPreview = !showPreview">
				{{ showPreview ? 'Hide preview' : 'Full preview' }}
			</button>
		</div>

		<div v-if="blocks.length === 0" class="alert alert-light border">
			No blocks yet. Tap <strong>+ Add block</strong> to start building your page.
		</div>

		<div class="lp-block-editor__layout" :class="{ 'lp-block-editor__layout--preview': showPreview }">
			<div class="lp-block-editor__blocks">
				<BlockCard
					v-for="(block, index) in blocks"
					:key="block.id"
					:block="block"
					:can-move-up="index > 0"
					:can-move-down="index < blocks.length - 1"
					@move-up="moveBlock(index, -1)"
					@move-down="moveBlock(index, 1)"
					@remove="removeBlock(index)"
				/>
			</div>

			<aside v-if="showPreview" class="lp-block-editor__full-preview">
				<h6 class="mb-3">Page preview</h6>
				<div class="lp-page-preview">
					<BlockPreview v-for="block in blocks" :key="`preview-${block.id}`" :block="block" />
				</div>
			</aside>
		</div>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { BLOCK_TYPES, createDefaultBlock } from '@/constants/landingPageBlocks'
import BlockCard from './BlockCard.vue'
import BlockPreview from './BlockPreview.vue'

const props = defineProps({
	modelValue: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

const showPreview = ref(false)

const blocks = computed({
	get: () => props.modelValue,
	set: (value) => emit('update:modelValue', value),
})

const addBlock = (type) => {
	const next = [...props.modelValue, createDefaultBlock(type)]
	emit('update:modelValue', next)
}

const moveBlock = (index, direction) => {
	const next = [...props.modelValue]
	const target = index + direction
	if (target < 0 || target >= next.length) return
	const temp = next[index]
	next[index] = next[target]
	next[target] = temp
	emit('update:modelValue', next)
}

const removeBlock = (index) => {
	const next = props.modelValue.filter((_, i) => i !== index)
	emit('update:modelValue', next)
}
</script>

<style scoped>
.lp-block-editor__toolbar {
	display: flex;
	flex-wrap: wrap;
	gap: 0.5rem;
	margin-bottom: 1rem;
}

.lp-block-editor__toolbar .btn {
	min-height: 2.75rem;
}

.lp-block-editor__layout--preview {
	display: grid;
	grid-template-columns: 1fr;
	gap: 1rem;
}

@media (min-width: 992px) {
	.lp-block-editor__layout--preview {
		grid-template-columns: 1fr 1fr;
	}
}

.lp-block-editor__full-preview {
	position: sticky;
	top: 1rem;
	align-self: start;
	border: 1px solid #dee2e6;
	border-radius: 0.5rem;
	padding: 1rem;
	background: #fafafa;
	max-height: calc(100vh - 2rem);
	overflow: auto;
}

.lp-page-preview {
	background: #fff;
	padding: 1rem;
	border-radius: 0.35rem;
}
</style>
