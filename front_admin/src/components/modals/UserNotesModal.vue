<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="userNotesLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document" style="max-width: 600px !important;">
			<div class="modal-content">
				<div class="modal-header justify-content-between">
					<div class="d-flex flex-column">
						<h5 class="modal-title mb-1" id="userNotesLabel">User Notes</h5>
						<p class="mb-0 text-muted small" id="user-name">
							{{ userName }}
						</p>
					</div>
					<div class="d-flex align-items-center gap-2">
						<button
							v-if="!showCreateForm"
							type="button"
							id="create-note"
							class="btn btn-sm btn-primary mr-2"
							@click="toggleCreateForm"
						>
							Create
						</button>
						<button
							v-else
							type="button"
							class="btn btn-sm btn-secondary mr-2"
							@click="cancelCreate"
						>
							Cancel
						</button>
					</div>
				</div>
				<div class="modal-body">

					<!-- Create Note Form -->
					<div
						v-if="showCreateForm"
						class="create-note-form mb-4 p-3 border rounded bg-light"
					>
						<label for="note-textarea" class="form-label fw-semibold mb-2">
							Add Note
						</label>
						<textarea
							id="note-textarea"
							v-model="newNoteText"
							class="form-control"
							rows="4"
							placeholder="Enter your note here..."
						></textarea>
						<div class="d-flex justify-content-end gap-2 mt-2">
							<button
								type="button"
								class="btn btn-sm btn-secondary"
								@click="cancelCreate"
							>
								Cancel
							</button>
							<button
								type="button"
								class="btn btn-sm btn-primary"
								:disabled="!newNoteText.trim()"
								@click="saveNote"
							>
								Save
							</button>
						</div>
					</div>

					<!-- Notes List -->
					<div v-if="!hasNotes && !showCreateForm" class="text-center py-4">
						<p class="text-muted mb-0">No notes were found.</p>
					</div>

					<div v-else-if="hasNotes" class="notes-list">
						<div
							v-for="note in notes"
							:key="note.id || note.created_at + note.text"
							class="note-item mb-3 p-3 border rounded bg-white"
						>
							<div class="d-flex justify-content-between align-items-start mb-2">
								<span class="text-muted small">
									{{ formatDateTime(note.created_at) }}
								</span>
							</div>
							<div class="note-text">{{ note.text }}</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-primary btn-fw"
						@click="close"
					>
						Close
					</button>
				</div>
			</div>
		</div>
	</div>
	<div
		v-if="show"
		class="modal-backdrop fade show"
		@click="close"
	></div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	user: {
		type: Object,
		default: null
	},
	notes: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits(['close', 'create', 'save'])

const showCreateForm = ref(false)
const newNoteText = ref('')

const close = () => {
	showCreateForm.value = false
	newNoteText.value = ''
	emit('close')
}

const toggleCreateForm = () => {
	showCreateForm.value = true
}

const cancelCreate = () => {
	showCreateForm.value = false
	newNoteText.value = ''
}

const saveNote = () => {
	if (!newNoteText.value.trim()) return
	
	emit('save', {
		user: props.user,
		text: newNoteText.value.trim()
	})
	
	newNoteText.value = ''
	showCreateForm.value = false
}

const userName = computed(() => {
	if (!props.user) return ''
	if (props.user.first_name || props.user.last_name) {
		return `${props.user.first_name ?? ''} ${props.user.last_name ?? ''}`.trim()
	}
	return props.user.name || ''
})

const hasNotes = computed(
	() => Array.isArray(props.notes) && props.notes.length > 0
)

const formatDateTime = (dateTime) => {
	if (!dateTime) return ''

	if (typeof dateTime === 'string' && /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(dateTime)) {
		return dateTime
	}

	const date = new Date(dateTime)
	if (isNaN(date.getTime())) return dateTime

	const year = date.getFullYear()
	const month = String(date.getMonth() + 1).padStart(2, '0')
	const day = String(date.getDate()).padStart(2, '0')
	const hours = String(date.getHours()).padStart(2, '0')
	const minutes = String(date.getMinutes()).padStart(2, '0')
	const seconds = String(date.getSeconds()).padStart(2, '0')

	return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

watch(() => props.show, (newVal) => {
	if (!newVal) {
		showCreateForm.value = false
		newNoteText.value = ''
	}
})
</script>

<style scoped>
.modal-backdrop {
	position: fixed;
	top: 0;
	left: 0;
	z-index: 1040;
	width: 100vw;
	height: 100vh;
	background-color: #000;
	opacity: 0.5;
}

.modal.show {
	display: block;
}

.create-note-form {
	background-color: #f8f9fa;
	border: 1px solid #dee2e6;
}

.note-item {
	transition: box-shadow 0.2s ease;
}

.note-item:hover {
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.note-text {
	white-space: pre-wrap;
	word-wrap: break-word;
	line-height: 1.5;
}

#note-textarea {
	resize: vertical;
	min-height: 100px;
}

.btn-fw {
	min-width: 100px;
}
</style>


