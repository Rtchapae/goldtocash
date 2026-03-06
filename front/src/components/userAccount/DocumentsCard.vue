<template>
	<div class="card p-2">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th colspan="10">Documents
						<span v-if="isLoading">
							<div class="spinner-border spinner-border-sm text-primary" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-if="documents.length === 0">
					<td class="d-flex flex-wrapped">
						<div class="w-100 text-center">No Documents Uploaded</div>
					</td>
				</tr>
				<tr v-else>
					<td class="d-flex flex-wrap">
					<div v-for="doc in documents" :key="doc.id" class="m-2 text-center file-card">
						<i class="far fa-file-image fa-3x"></i>
						<p class="mt-3 text-center">{{ getDocumentTypeLabel(doc) }}</p>
						<button
							class="btn btn-sm btn-block btn-primary media-display-button-disable mb-3"
							@click="viewDocument(doc)"
							type="button"
						>
							View
						</button>
					</div>
					</td>
				</tr>
				<tr>
					<td colspan="10" class="text-center">
						<a href="#" @click.prevent="$emit('upload')" title="Upload">Upload a Document</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useToast } from '@/composables/useToast'

defineEmits(['upload'])
defineProps({
	documents: { type: Array, required: true },
	isLoading: { type: Boolean, default: false },
})

const router = useRouter()
const toast = useToast()

const getDocumentTypeLabel = (doc) => {
	if (doc.title) {
		return doc.title
	}
	
	if (doc.type) {
		const typeMap = {
			license: 'Driver License',
			passport: 'Passport',
			military: 'Military ID',
			state: 'State ID',
		}
		return typeMap[doc.type] || doc.type
	}
	
	return 'Document'
}

const viewDocument = async (doc) => {
	try {
		const token = localStorage.getItem('jwt_token')
		if (!token) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
			return
		}

		let url = doc.viewUrl

		if (!url) {
			const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1'
			url = `${API_BASE_URL}/front/user/documents/${doc.id}/view`
		}

		if (!/^https?:\/\//i.test(url)) {
			const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1'
			const base = API_BASE_URL.replace(/\/$/, '')
			const path = url.replace(/^\//, '')
			url = `${base}/${path}`
		}

		const response = await fetch(url, {
			headers: {
				'Authorization': `Bearer ${token}`,
			},
		})

		if (!response.ok) {
			if (response.status === 401) {
				localStorage.removeItem('jwt_token')
				toast.error('Session expired. Please sign in again.')
				router.push('/sign-in')
				return
			}
			if (response.status === 404) {
				toast.error('This document is not available. If you just requested a kit, it may still be generating—please try again in a few minutes or contact support.')
				return
			}
			const errorData = await response.json().catch(() => ({
				message: `Failed to load document: ${response.status}`
			}))
			throw new Error(errorData.message || `Failed to load document: ${response.status}`)
		}

		const blob = await response.blob()
		
		const blobUrl = URL.createObjectURL(blob)
		window.open(blobUrl, '_blank')
		
		setTimeout(() => {
			URL.revokeObjectURL(blobUrl)
		}, 1000)
	} catch (error) {
		console.error('Failed to view document:', error)
		toast.error(error?.message || 'Failed to open document. Please try again.')
	}
}
</script>

<style scoped>
.file-card {
	max-width: 150px;
	padding: 1rem;
	border: 1px solid #ddd;
	border-radius: 0.25rem;
}

.card {
	border: 1px solid #ddd;
	border-radius: 0.25rem;
	margin-bottom: 1rem;
}
</style>


