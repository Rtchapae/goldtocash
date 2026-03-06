<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="SMS" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body">
							<SmsSearchFilters
								:filters="filters"
								:loading="isLoading"
								:has-active-filters="hasActiveFilters"
								@update:filters="handleFiltersUpdate"
								@search="handleSearch"
								@clear="handleClear"
							/>

							<hr />

							<SmsMessagesTable
								:messages="messages"
								:loading="isLoading"
								:current-page="currentPage"
								:total-items="totalItems"
								:per-page="perPage"
								:last-page="lastPage"
								@page-change="handlePageChange"
								@per-page-change="handlePerPageChange"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { searchSms } from '@/api/sms'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import { SmsSearchFilters, SmsMessagesTable } from '@/components/sms'
import { useToast } from '@/composables/useToast'

const { showToast } = useToast()

const isLoading = ref(false)
const messages = ref([])
const hasSearched = ref(false)

const currentPage = ref(1)
const perPage = ref(20)
const totalItems = ref(0)
const lastPage = ref(1)

const filters = ref({
	from: '',
	to: '',
	mid: ''
})

const hasActiveFilters = computed(() => {
	return hasSearched.value && (filters.value.from || filters.value.to || filters.value.mid)
})

const handleSearch = async () => {
	currentPage.value = 1
	hasSearched.value = true
	await loadMessages()
}

const loadMessages = async () => {
	try {
		isLoading.value = true

		const params = {
			...filters.value,
			page: currentPage.value,
			per_page: perPage.value
		}

		Object.keys(params).forEach(key => {
			if (params[key] === '' || params[key] === null || params[key] === undefined) {
				delete params[key]
			}
		})

		const response = await searchSms(params)

		if (response?.data) {
			messages.value = response.data.data || []
			
			if (response.data.meta) {
				currentPage.value = response.data.meta.current_page || 1
				totalItems.value = response.data.meta.total || 0
				lastPage.value = response.data.meta.last_page || 1
				perPage.value = response.data.meta.per_page || 20
			}
		} else {
			messages.value = []
		}
	} catch (error) {
		messages.value = []
		
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load SMS messages'
		
		showToast(errorMessage, 'error')
	} finally {
		isLoading.value = false
	}
}

const handleFiltersUpdate = (newFilters) => {
	filters.value = { ...newFilters }
}

const handleClear = async () => {
	filters.value = {
		from: '',
		to: '',
		mid: ''
	}
	currentPage.value = 1
	hasSearched.value = false
	await loadMessages()
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadMessages()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadMessages()
}

onMounted(() => {
	handleSearch()
})
</script>
