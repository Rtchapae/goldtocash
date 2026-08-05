<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader
				:title="isEditMode ? 'Edit page' : 'Create page'"
				:loading="isLoading || isSaving"
				back-to="/builder-pages"
				back-label="Back to pages"
			/>

			<div class="row">
				<div class="col-lg-12">
					<div class="card mb-3">
						<div class="card-body">
							<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
								<div class="d-flex flex-wrap gap-2">
									<label class="btn btn-outline-primary mb-0">
										Import Word (.docx)
										<input
											type="file"
											accept=".doc,.docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
											class="d-none"
											:disabled="isImporting"
											@change="onDocxSelected"
										/>
									</label>
									<span v-if="isImporting" class="align-self-center text-muted small">Importing…</span>
								</div>
								<div class="d-flex flex-wrap gap-2">
									<button type="button" class="btn btn-outline-secondary" :disabled="isSaving" @click="save(false)">
										Save draft
									</button>
									<button type="button" class="btn btn-primary" :disabled="isSaving" @click="save(true)">
										{{ form.published ? 'Update & keep published' : 'Publish' }}
									</button>
								</div>
							</div>

							<div class="row g-3">
								<div class="col-md-6">
									<label class="form-label">Title</label>
									<input v-model="form.title" type="text" class="form-control" @blur="maybeSlugFromTitle" />
								</div>
								<div class="col-md-6">
									<label class="form-label">Slug (URL)</label>
									<div class="input-group">
										<span class="input-group-text">/</span>
										<input v-model="form.slug" type="text" class="form-control" placeholder="sell-scrap-gold" />
									</div>
									<small class="text-muted">Live at goldtocash.us/{{ form.slug || '…' }}</small>
								</div>
								<div class="col-md-6">
									<label class="form-label">SEO title</label>
									<input v-model="form.seo_title" type="text" class="form-control" />
								</div>
								<div class="col-md-6">
									<label class="form-label">SEO description</label>
									<input v-model="form.seo_description" type="text" class="form-control" maxlength="320" />
								</div>
								<div class="col-12">
									<div class="form-check">
										<input id="pb-published" v-model="form.published" class="form-check-input" type="checkbox" />
										<label class="form-check-label" for="pb-published">Published</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card mb-3">
						<div class="card-body">
							<div class="d-flex flex-wrap gap-2 align-items-center mb-3">
								<strong class="me-2">Add block:</strong>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('heading')">Heading</button>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('richtext')">Text</button>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('image')">Image</button>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('calculator')">Calculator</button>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('form')">Form</button>
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('faq')">FAQ</button>
							</div>

							<div v-if="!form.blocks.length" class="text-muted border rounded p-4 text-center">
								No blocks yet. Import a Word doc or add blocks manually.
							</div>

							<div
								v-for="(block, index) in form.blocks"
								:id="`pb-block-${block.id}`"
								:key="block.id"
								class="border rounded p-3 mb-3 bg-light pb-block-card"
								:class="{ 'pb-block-card--flash': flashBlockId === block.id }"
							>
								<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
									<span class="badge bg-dark text-uppercase">{{ block.type }}</span>
									<div class="btn-group btn-group-sm">
										<button type="button" class="btn btn-outline-secondary" :disabled="index === 0" @click="moveBlock(index, -1)">↑</button>
										<button type="button" class="btn btn-outline-secondary" :disabled="index === form.blocks.length - 1" @click="moveBlock(index, 1)">↓</button>
										<button type="button" class="btn btn-outline-danger" @click="removeBlock(index)">Remove</button>
									</div>
								</div>

								<template v-if="block.type === 'heading'">
									<div class="row g-2">
										<div class="col-md-2">
											<label class="form-label">Level</label>
											<select v-model.number="block.data.level" class="form-select">
												<option :value="1">H1</option>
												<option :value="2">H2</option>
												<option :value="3">H3</option>
											</select>
										</div>
										<div class="col-md-10">
											<label class="form-label">Text</label>
											<input v-model="block.data.text" type="text" class="form-control" />
										</div>
									</div>
								</template>

								<template v-else-if="block.type === 'richtext'">
									<label class="form-label">Default font for block</label>
									<select v-model="block.data.fontFamily" class="form-select mb-2" style="max-width: 320px;">
										<option value="">Montserrat (site default)</option>
										<option value="Arial, Helvetica, sans-serif">Arial</option>
										<option value="Georgia, Palatino, serif">Georgia</option>
										<option value="'Times New Roman', Times, serif">Times New Roman</option>
										<option value="Verdana, Geneva, sans-serif">Verdana</option>
										<option value="'Courier New', Courier, monospace">Courier New</option>
									</select>
									<BuilderTinyEditor v-model="block.data.html" :height="300" />
								</template>

								<template v-else-if="block.type === 'image'">
									<div class="row g-2 align-items-end">
										<div class="col-md-8">
											<label class="form-label">Image URL</label>
											<input v-model="block.data.url" type="text" class="form-control" />
										</div>
										<div class="col-md-4">
											<label class="btn btn-outline-primary w-100 mb-0">
												Upload
												<input type="file" accept="image/*" class="d-none" @change="onImageUpload(block, $event)" />
											</label>
										</div>
										<div class="col-md-12">
											<label class="form-label">Alt text</label>
											<input v-model="block.data.alt" type="text" class="form-control" />
										</div>
										<div class="col-md-3">
											<label class="form-label">Alignment</label>
											<select v-model="block.data.align" class="form-select">
												<option value="center">Center</option>
												<option value="left">Left</option>
												<option value="right">Right</option>
											</select>
										</div>
										<div class="col-md-3">
											<label class="form-label">Width (px)</label>
											<input
												v-model.number="block.data.widthPx"
												type="number"
												min="40"
												max="1600"
												step="10"
												class="form-control"
												placeholder="auto"
											/>
										</div>
										<div class="col-md-3">
											<label class="form-label">Height (px)</label>
											<input
												v-model.number="block.data.heightPx"
												type="number"
												min="40"
												max="1600"
												step="10"
												class="form-control"
												placeholder="auto"
											/>
										</div>
										<div class="col-md-3">
											<label class="form-label">Fit</label>
											<select v-model="block.data.objectFit" class="form-select">
												<option value="contain">Contain</option>
												<option value="cover">Cover</option>
												<option value="fill">Stretch</option>
											</select>
										</div>
										<div v-if="block.data.url" class="col-12">
											<p class="small text-muted mb-2">
												Drag the corner handle to resize. Hold Shift to freely change width/height.
											</p>
											<div class="pb-image-preview" :style="imagePreviewStyle(block)">
												<div class="pb-image-resize" :style="imageResizeBoxStyle(block)">
													<img
														:src="resolveAdminAssetUrl(block.data.url)"
														alt=""
														draggable="false"
														:style="imagePreviewImgStyle(block)"
														@load="onImagePreviewLoad(block, $event)"
													/>
													<span
														class="pb-image-resize__handle"
														title="Drag to resize"
														@mousedown.prevent="startImageResize(block, $event)"
													/>
												</div>
											</div>
										</div>
									</div>
								</template>

								<template v-else-if="block.type === 'calculator'">
									<label class="form-label">Calculator variant</label>
									<select v-model="block.data.variant" class="form-select" style="max-width: 280px;">
										<option value="default">Default (with market image)</option>
										<option value="tabbed">Compact (tabbed style)</option>
									</select>
									<p class="small text-muted mb-0 mt-2">Embeds the site gold value calculator on the public page.</p>
								</template>

								<template v-else-if="block.type === 'form'">
									<label class="form-label">Kit form style</label>
									<select v-model="block.data.template" class="form-select" style="max-width: 280px;">
										<option value="modern">Modern</option>
										<option value="traditional">Traditional</option>
										<option value="sophisticated">Sophisticated</option>
									</select>
									<p class="small text-muted mb-0 mt-2">Embeds the Request Free Kit form (same as blog posts).</p>
								</template>

								<template v-else-if="block.type === 'faq'">
									<div class="d-flex justify-content-between align-items-center mb-2">
										<label class="form-label mb-0">FAQ items</label>
										<button type="button" class="btn btn-sm btn-outline-primary" @click="addFaqItem(block)">+ Item</button>
									</div>
									<div
										v-for="(item, faqIdx) in block.data.items"
										:key="faqIdx"
										class="border rounded p-2 mb-2 bg-white pb-faq-item"
									>
										<div class="d-flex justify-content-between mb-2">
											<strong class="small">#{{ faqIdx + 1 }}</strong>
											<button type="button" class="btn btn-sm btn-link text-danger p-0" @click="block.data.items.splice(faqIdx, 1)">Remove</button>
										</div>
										<input v-model="item.question" type="text" class="form-control form-control-sm mb-2" placeholder="Question" />
										<label class="form-label small mb-1">Answer</label>
										<BuilderTinyEditor v-model="item.answer" :height="160" compact />
									</div>
								</template>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import BuilderTinyEditor from '@/components/builder/BuilderTinyEditor.vue'
import {
	getBuilderPage,
	createBuilderPage,
	updateBuilderPage,
	uploadBuilderPageImage,
	importBuilderPageDocx,
} from '@/api/builderPages'
import { resolveAdminAssetUrl } from '@/utils/apiAssetUrl'
import { useToast } from '@/composables/useToast'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isLoading = ref(false)
const isSaving = ref(false)
const isImporting = ref(false)
const slugTouched = ref(false)
const flashBlockId = ref(null)

const isEditMode = computed(() => !!route.params.id)
const pageId = computed(() => (route.params.id ? Number(route.params.id) : null))

const form = ref({
	title: '',
	slug: '',
	seo_title: '',
	seo_description: '',
	published: false,
	blocks: [],
})

const newId = () => {
	if (typeof crypto !== 'undefined' && crypto.randomUUID) {
		return crypto.randomUUID()
	}
	return `b-${Date.now()}-${Math.random().toString(16).slice(2)}`
}

const emptyBlock = (type) => {
	const id = newId()
	if (type === 'heading') return { id, type, data: { level: 2, text: '' } }
	if (type === 'richtext') return { id, type, data: { html: '<p></p>', fontFamily: '' } }
	if (type === 'image') {
		return {
			id,
			type,
			data: {
				url: '',
				alt: '',
				align: 'center',
				widthPx: null,
				heightPx: null,
				objectFit: 'contain',
				widthPercent: 100,
				maxWidth: null,
			},
		}
	}
	if (type === 'calculator') return { id, type, data: { variant: 'default' } }
	if (type === 'form') return { id, type, data: { template: 'modern' } }
	if (type === 'faq') {
		return {
			id,
			type,
			data: {
				items: [{ question: '', answer: '' }],
			},
		}
	}
	return { id, type, data: {} }
}

const scrollToBlock = async (blockId) => {
	await nextTick()
	await nextTick()
	const el = document.getElementById(`pb-block-${blockId}`)
	if (!el) return
	el.scrollIntoView({ behavior: 'smooth', block: 'center' })
	flashBlockId.value = blockId
	window.setTimeout(() => {
		if (flashBlockId.value === blockId) flashBlockId.value = null
	}, 1600)
}

const addBlock = async (type) => {
	const block = emptyBlock(type)
	form.value.blocks.push(block)
	await scrollToBlock(block.id)
}

const removeBlock = (index) => {
	form.value.blocks.splice(index, 1)
}

const moveBlock = (index, delta) => {
	const target = index + delta
	if (target < 0 || target >= form.value.blocks.length) return
	const list = form.value.blocks
	const [item] = list.splice(index, 1)
	list.splice(target, 0, item)
}

const addFaqItem = (block) => {
	if (!Array.isArray(block.data.items)) block.data.items = []
	block.data.items.push({ question: '', answer: '' })
}

const imageResizeState = ref(null)

const imagePreviewStyle = (block) => {
	const align = block.data?.align || 'center'
	return { textAlign: align }
}

const imageDisplaySize = (block) => {
	const widthPx = Number(block.data?.widthPx) || Number(block.data?.maxWidth) || 0
	const heightPx = Number(block.data?.heightPx) || 0
	return {
		widthPx: widthPx > 0 ? widthPx : null,
		heightPx: heightPx > 0 ? heightPx : null,
	}
}

const imageResizeBoxStyle = (block) => {
	const { widthPx, heightPx } = imageDisplaySize(block)
	return {
		width: widthPx ? `${widthPx}px` : 'auto',
		height: heightPx ? `${heightPx}px` : 'auto',
		maxWidth: '100%',
	}
}

const imagePreviewImgStyle = (block) => {
	const { widthPx, heightPx } = imageDisplaySize(block)
	const fit = block.data?.objectFit || 'contain'
	return {
		width: widthPx || heightPx ? '100%' : 'auto',
		height: heightPx ? '100%' : 'auto',
		maxWidth: '100%',
		objectFit: heightPx || widthPx ? fit : 'contain',
		display: 'block',
	}
}

const onImagePreviewLoad = (block, event) => {
	if (block.data?.widthPx || block.data?.heightPx || block.data?.maxWidth) return
	const img = event.target
	if (!img?.naturalWidth) return
	const maxPreview = 480
	const scale = Math.min(1, maxPreview / img.naturalWidth)
	block.data.widthPx = Math.round(img.naturalWidth * scale)
	block.data.heightPx = Math.round(img.naturalHeight * scale)
}

const startImageResize = (block, event) => {
	const box = event.currentTarget.parentElement
	if (!box) return
	const rect = box.getBoundingClientRect()
	imageResizeState.value = {
		block,
		startX: event.clientX,
		startY: event.clientY,
		startW: rect.width,
		startH: rect.height,
		lockAspect: !event.shiftKey,
		ratio: rect.width / Math.max(1, rect.height),
	}
	document.body.classList.add('pb-image-resizing')
	window.addEventListener('mousemove', onImageResizeMove)
	window.addEventListener('mouseup', endImageResize)
}

const onImageResizeMove = (event) => {
	const state = imageResizeState.value
	if (!state) return
	const dx = event.clientX - state.startX
	const dy = event.clientY - state.startY
	let w = Math.max(40, Math.min(1600, Math.round(state.startW + dx)))
	let h = Math.max(40, Math.min(1600, Math.round(state.startH + dy)))
	const lockAspect = state.lockAspect && !event.shiftKey
	if (lockAspect) {
		if (Math.abs(dx) >= Math.abs(dy)) {
			h = Math.max(40, Math.round(w / state.ratio))
		} else {
			w = Math.max(40, Math.round(h * state.ratio))
		}
	}
	state.block.data.widthPx = w
	state.block.data.heightPx = h
	state.block.data.maxWidth = w
}

const endImageResize = () => {
	if (!imageResizeState.value) return
	imageResizeState.value = null
	document.body.classList.remove('pb-image-resizing')
	window.removeEventListener('mousemove', onImageResizeMove)
	window.removeEventListener('mouseup', endImageResize)
}

onBeforeUnmount(endImageResize)

const slugify = (value) =>
	String(value || '')
		.toLowerCase()
		.trim()
		.replace(/[^a-z0-9]+/g, '-')
		.replace(/^-+|-+$/g, '')

const maybeSlugFromTitle = () => {
	if (slugTouched.value && form.value.slug) return
	if (!form.value.slug) {
		form.value.slug = slugify(form.value.title)
	}
}

const onImageUpload = async (block, event) => {
	const file = event.target.files?.[0]
	event.target.value = ''
	if (!file) return
	try {
		const res = await uploadBuilderPageImage(file)
		block.data.url = res?.data?.url || ''
		toast.success('Image uploaded')
	} catch (e) {
		toast.error(e.message || 'Upload failed')
	}
}

const normalizeImportedBlocks = (blocks) =>
	(Array.isArray(blocks) ? blocks : []).map((b) => {
		if (b.type === 'richtext') {
			return {
				...b,
				data: {
					fontFamily: b.data?.fontFamily || '',
					html: b.data?.html || '<p></p>',
				},
			}
		}
		if (b.type === 'image') {
			return {
				...b,
				data: {
					url: b.data?.url || '',
					alt: b.data?.alt || '',
					align: b.data?.align || 'center',
					widthPx: b.data?.widthPx ?? b.data?.maxWidth ?? null,
					heightPx: b.data?.heightPx ?? null,
					objectFit: b.data?.objectFit || 'contain',
					widthPercent: b.data?.widthPercent ?? 100,
					maxWidth: b.data?.maxWidth ?? b.data?.widthPx ?? null,
				},
			}
		}
		if (b.type === 'form') {
			return {
				...b,
				data: { template: b.data?.template || 'modern' },
			}
		}
		if (b.type === 'faq') {
			return {
				...b,
				data: {
					items: (b.data?.items || []).map((it) => ({
						question: it.question || '',
						answer: it.answer || '',
					})),
				},
			}
		}
		return b
	})

const onDocxSelected = async (event) => {
	const file = event.target.files?.[0]
	event.target.value = ''
	if (!file) return
	isImporting.value = true
	try {
		const res = await importBuilderPageDocx(file)
		const data = res.data || res
		form.value.title = data.title || form.value.title
		form.value.slug = data.slug || slugify(data.title)
		if (data.seo_description) {
			form.value.seo_description = data.seo_description
		}
		form.value.blocks = normalizeImportedBlocks(data.blocks)
		slugTouched.value = true
		toast.success('Word document imported — review blocks, then publish')
		await nextTick()
		if (form.value.blocks[0]) {
			await scrollToBlock(form.value.blocks[0].id)
		}
	} catch (e) {
		toast.error(e.message || 'Import failed')
	} finally {
		isImporting.value = false
	}
}

const payload = () => ({
	title: form.value.title,
	slug: form.value.slug || slugify(form.value.title),
	seo_title: form.value.seo_title || null,
	seo_description: form.value.seo_description || null,
	published: !!form.value.published,
	blocks: form.value.blocks,
})

const save = async (publish) => {
	if (!form.value.title.trim()) {
		toast.error('Title is required')
		return
	}
	if (publish) form.value.published = true
	isSaving.value = true
	try {
		const body = payload()
		if (isEditMode.value) {
			await updateBuilderPage(pageId.value, body)
			toast.success(publish ? 'Page published' : 'Draft saved')
		} else {
			const res = await createBuilderPage(body)
			const created = res.data || res
			toast.success(publish ? 'Page published' : 'Draft saved')
			await router.replace(`/builder-pages/edit/${created.id}`)
		}
	} catch (e) {
		toast.error(e.message || 'Save failed')
	} finally {
		isSaving.value = false
	}
}

const loadPage = async () => {
	if (!isEditMode.value) return
	isLoading.value = true
	try {
		const res = await getBuilderPage(pageId.value)
		const data = res.data || res
		form.value = {
			title: data.title || '',
			slug: data.slug || '',
			seo_title: data.seo_title || '',
			seo_description: data.seo_description || '',
			published: !!data.published,
			blocks: normalizeImportedBlocks(data.blocks),
		}
		slugTouched.value = true
	} catch (e) {
		toast.error(e.message || 'Failed to load page')
	} finally {
		isLoading.value = false
	}
}

onMounted(loadPage)
</script>

<style scoped>
.pb-block-card--flash {
	box-shadow: 0 0 0 3px rgba(195, 158, 61, 0.55);
	transition: box-shadow 0.3s ease;
}

.pb-image-preview {
	overflow: auto;
	padding: 4px 12px 12px 4px;
}

.pb-image-resize {
	position: relative;
	display: inline-block;
	vertical-align: top;
	border: 1px dashed #c39e3d;
	background: #fafafa;
	line-height: 0;
	user-select: none;
}

.pb-image-resize img {
	pointer-events: none;
	user-select: none;
}

.pb-image-resize__handle {
	position: absolute;
	right: -7px;
	bottom: -7px;
	width: 14px;
	height: 14px;
	border-radius: 2px;
	background: #c39e3d;
	border: 2px solid #fff;
	box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.25);
	cursor: nwse-resize;
	z-index: 2;
}

.pb-faq-item :deep(.tox-tinymce) {
	border-radius: 4px;
}

.pb-faq-item :deep(.tox .tox-edit-area__iframe) {
	background: #fff;
}
</style>

<style>
body.pb-image-resizing {
	cursor: nwse-resize !important;
	user-select: none !important;
}

body.pb-image-resizing * {
	cursor: nwse-resize !important;
	user-select: none !important;
}
</style>
