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
								<button type="button" class="btn btn-sm btn-outline-dark" @click="addBlock('faq')">FAQ</button>
							</div>

							<div v-if="!form.blocks.length" class="text-muted border rounded p-4 text-center">
								No blocks yet. Import a Word doc or add blocks manually.
							</div>

							<div
								v-for="(block, index) in form.blocks"
								:key="block.id"
								class="border rounded p-3 mb-3 bg-light"
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
									<div class="btn-group btn-group-sm mb-2">
										<button type="button" class="btn btn-outline-secondary" @click="execFormat(block.id, 'bold')"><b>B</b></button>
										<button type="button" class="btn btn-outline-secondary" @click="execFormat(block.id, 'italic')"><i>I</i></button>
										<button type="button" class="btn btn-outline-secondary" @click="execFormat(block.id, 'underline')"><u>U</u></button>
									</div>
									<div
										:ref="(el) => setEditorRef(block.id, el)"
										class="form-control pb-rich-editor"
										contenteditable="true"
										@blur="onRichBlur(block, $event)"
										v-html="block.data.html"
									/>
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
										<div v-if="block.data.url" class="col-12">
											<img :src="resolveAdminAssetUrl(block.data.url)" alt="" class="img-fluid rounded border" style="max-height: 220px;" />
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

								<template v-else-if="block.type === 'faq'">
									<div class="d-flex justify-content-between align-items-center mb-2">
										<label class="form-label mb-0">FAQ items</label>
										<button type="button" class="btn btn-sm btn-outline-primary" @click="addFaqItem(block)">+ Item</button>
									</div>
									<div
										v-for="(item, faqIdx) in block.data.items"
										:key="faqIdx"
										class="border rounded p-2 mb-2 bg-white"
									>
										<div class="d-flex justify-content-between mb-2">
											<strong class="small">#{{ faqIdx + 1 }}</strong>
											<button type="button" class="btn btn-sm btn-link text-danger p-0" @click="block.data.items.splice(faqIdx, 1)">Remove</button>
										</div>
										<input v-model="item.question" type="text" class="form-control mb-2" placeholder="Question" />
										<textarea v-model="item.answer" class="form-control" rows="2" placeholder="Answer" />
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
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
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
const editorRefs = {}

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
	if (type === 'richtext') return { id, type, data: { html: '<p></p>' } }
	if (type === 'image') return { id, type, data: { url: '', alt: '' } }
	if (type === 'calculator') return { id, type, data: { variant: 'default' } }
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

const addBlock = (type) => {
	form.value.blocks.push(emptyBlock(type))
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

const setEditorRef = (id, el) => {
	if (el) editorRefs[id] = el
}

const onRichBlur = (block, event) => {
	block.data.html = event.target.innerHTML
}

const execFormat = (blockId, command) => {
	const el = editorRefs[blockId]
	if (el) el.focus()
	document.execCommand(command, false, null)
	const block = form.value.blocks.find((b) => b.id === blockId)
	if (block && el) block.data.html = el.innerHTML
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
		form.value.blocks = Array.isArray(data.blocks) ? data.blocks : []
		slugTouched.value = true
		toast.success('Word document imported — review blocks, then publish')
		await nextTick()
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
			blocks: Array.isArray(data.blocks) ? data.blocks : [],
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
.pb-rich-editor {
	min-height: 120px;
	background: #fff;
	overflow: auto;
}
.pb-rich-editor:focus {
	outline: none;
	border-color: #86b7fe;
	box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
