<template>
	<div class="card basic-data-table" :class="{ 'table-dark-mode': isDarkMode }">
		<div
			v-if="showTitle && title"
			class="card-header"
		>
			<h5 class="card-title mb-0">{{ title }}</h5>
		</div>
		<div class="card-body dt-container">
			<div class="dt-layout-row mb-3" v-if="showLengthControl || showSearchControl">
				<div class="dt-layout-cell dt-start" v-if="showLengthControl">
					<div class="dt-length d-flex align-items-center gap-2">
						<label class="mb-0">Show</label>
						<select
							class="dt-input form-select form-select-sm"
							:value="pageSize"
							@change="handlePerPageChange"
						>
							<option
								v-for="size in pageSizeOptions"
								:key="size"
								:value="size"
								:selected="size === pageSize"
							>
								{{ size }}
							</option>
						</select>
						<label class="mb-0">entries per page</label>
						<slot name="export-controls"></slot>
					</div>
				</div>
				<div class="dt-layout-cell dt-end" v-if="showSearchControl">
					<div class="dt-search">
						<label for="dt-search">Search:</label>
						<input
							id="dt-search"
							v-model="searchTerm"
							type="search"
							class="dt-input"
							placeholder=""
						>
					</div>
				</div>
			</div>

			<div class="dt-layout-row dt-layout-table">
				<div class="dt-layout-cell">
					<div
						ref="scrollContainerRef"
						class="table-responsive table-wrapper"
						:class="{ 'table-wrapper--infinite': infiniteScroll }"
						:style="scrollContainerStyle"
						@scroll.passive="onScrollContainer"
					>
						<div v-if="loading" class="table-loading-overlay">
							<div class="table-loading">
								<div class="loading-dots">
									<span></span>
									<span></span>
									<span></span>
								</div>
							</div>
						</div>
						<table
							:class="tableClass"
							:id="tableId"
							:data-page-length="pageSize"
						>
							<thead>
								<tr>
									<th
										v-if="showIndex"
										scope="col"
										class="dt-orderable-asc dt-orderable-desc"
										:class="{ [CSS_CLASS_ORDERING_ASC]: isSortedIndex && sortDirection === SORT_DIRECTION_ASC, [CSS_CLASS_ORDERING_DESC]: isSortedIndex && sortDirection === SORT_DIRECTION_DESC }"
										@click="toggleSort(indexKey)"
										role="columnheader"
										tabindex="0"
									>
										<span class="dt-column-title" role="button">
											<div class="form-check style-check d-flex align-items-center">
												<input class="form-check-input" type="checkbox">
												<label class="form-check-label">
													{{ indexLabel }}
												</label>
											</div>
										</span>
										<span class="dt-column-order"></span>
									</th>
									<th
										v-for="column in columns"
										:key="column.key"
										scope="col"
										:class="headerClass(column)"
										role="columnheader"
										tabindex="0"
										@click="onHeaderClick(column)"
									>
										<span class="dt-column-title" role="button">
											<slot
												:name="`header-${column.key}`"
												:column="column"
											>
												{{ column.label }}
											</slot>
											<i
												v-if="sortable && column.sortable !== false"
												:class="sortIconClass(column.key)"
												class="ms-1"
											/>
										</span>
										<span class="dt-column-order"></span>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="paginatedRows.length === 0">
									<td :colspan="totalColspan" class="text-center py-5 text-muted">
										{{ emptyMessage }}
									</td>
								</tr>
								<template v-else>
									<template v-for="(row, index) in paginatedRows" :key="row[idKey] ?? index">
										<tr>
											<td v-if="showIndex" class="sorting_1">
												<div class="form-check style-check d-flex align-items-center">
													<input class="form-check-input" type="checkbox">
													<label class="form-check-label">
														{{ globalIndex(index) }}
													</label>
												</div>
											</td>
											<td
												v-for="column in columns"
												:key="column.key"
												:class="column.class"
											>
												<slot
													:name="`cell-${column.key}`"
													:row="row"
													:index="index"
												>
													{{ row[column.key] }}
												</slot>
											</td>
										</tr>
										<tr
											v-if="infiniteScroll && index === loadMoreTriggerIndex"
											class="infinite-scroll-sentinel-row"
										>
											<td :colspan="totalColspan" class="p-0 border-0">
												<div :ref="bindSentinelRef" class="infinite-scroll-sentinel" aria-hidden="true"></div>
											</td>
										</tr>
									</template>
								</template>
							</tbody>
						</table>
						<div
							v-if="infiniteScroll && loadingMore"
							class="infinite-scroll-loading text-center py-2 text-muted"
						>
							<div class="loading-dots d-inline-flex">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="dt-layout-row mt-3" v-if="showInfo || (showPagination && !infiniteScroll)">
				<div class="dt-layout-cell dt-start" v-if="showInfo">
					<div class="dt-info">
						<template v-if="infiniteScroll">
							Loaded {{ loadedCount }} of {{ totalEntries }} entries
							<span v-if="hasMore && !loading && !loadingMore" class="text-muted"> — scroll for more</span>
						</template>
						<template v-else>
							Showing {{ startEntry }} to {{ endEntry }} of {{ totalEntries }} entries
						</template>
					</div>
				</div>
				<div class="dt-layout-cell dt-end" v-if="showPagination && !infiniteScroll && totalPages > 1">
					<div class="dt-paging paging_full_numbers">
						<button
							class="dt-paging-button first"
							type="button"
							:class="{ disabled: currentPage === DEFAULT_PAGE }"
							@click="goToPage(DEFAULT_PAGE)"
						>
							«
						</button>
						<button
							class="dt-paging-button previous"
							type="button"
							:class="{ disabled: currentPage === DEFAULT_PAGE }"
							@click="goToPage(currentPage - 1)"
						>
							‹
						</button>
						<template v-for="page in pages" :key="page">
							<button
								v-if="page !== PAGES_ELLIPSIS"
								class="dt-paging-button"
								type="button"
								:class="{ current: page === currentPage }"
								@click="goToPage(page)"
							>
								{{ page }}
							</button>
							<span v-else class="dt-paging-ellipsis">{{ PAGES_ELLIPSIS }}</span>
						</template>
						<button
							class="dt-paging-button next"
							type="button"
							:class="{ disabled: currentPage === totalPages }"
							@click="goToPage(currentPage + 1)"
						>
							›
						</button>
						<button
							class="dt-paging-button last"
							type="button"
							:class="{ disabled: currentPage === totalPages }"
							@click="goToPage(totalPages)"
						>
							»
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import {
	DEFAULT_PAGE_SIZE,
	DEFAULT_SERVER_PER_PAGE,
	DEFAULT_PAGE,
	DEFAULT_LAST_PAGE,
	DEFAULT_PAGE_SIZE_OPTIONS,
	MAX_PAGES_TO_SHOW_ALL,
	PAGES_ELLIPSIS_THRESHOLD,
	PAGES_AROUND_CURRENT,
	PAGES_ELLIPSIS,
	DEFAULT_SORT_DIRECTION,
	SORT_DIRECTION_ASC,
	SORT_DIRECTION_DESC,
	INDEX_KEY,
	DEFAULT_INDEX_LABEL,
	DEFAULT_ID_KEY,
	DEFAULT_TABLE_CLASS,
	DEFAULT_TABLE_ID,
	DEFAULT_EMPTY_MESSAGE,
	SORT_ICON_DEFAULT,
	SORT_ICON_ASC,
	SORT_ICON_DESC,
	CSS_CLASS_ORDERABLE_ASC,
	CSS_CLASS_ORDERABLE_DESC,
	CSS_CLASS_ORDERING_ASC,
	CSS_CLASS_ORDERING_DESC,
} from '@/config/table'

const emit = defineEmits(['update:page-size', 'page-change', 'per-page-change', 'load-more'])

const props = defineProps({
	title: {
		type: String,
		default: ''
	},
	rows: {
		type: Array,
		default: () => []
	},
	columns: {
		type: Array,
		default: () => []
	},
	showIndex: {
		type: Boolean,
		default: true
	},
	indexLabel: {
		type: String,
		default: DEFAULT_INDEX_LABEL
	},
	idKey: {
		type: String,
		default: DEFAULT_ID_KEY
	},
	tableClass: {
		type: String,
		default: DEFAULT_TABLE_CLASS
	},
	tableId: {
		type: String,
		default: DEFAULT_TABLE_ID
	},
	pageSizeOptions: {
		type: Array,
		default: () => DEFAULT_PAGE_SIZE_OPTIONS
	},
	defaultPageSize: {
		type: Number,
		default: DEFAULT_PAGE_SIZE
	},
	showLengthControl: {
		type: Boolean,
		default: true
	},
	showSearchControl: {
		type: Boolean,
		default: true
	},
	showInfo: {
		type: Boolean,
		default: true
	},
	showPagination: {
		type: Boolean,
		default: true
	},
	sortable: {
		type: Boolean,
		default: true
	},
	showTitle: {
		type: Boolean,
		default: true
	},
	// Server-side pagination props
	serverPagination: {
		type: Boolean,
		default: false
	},
	currentPage: {
		type: Number,
		default: DEFAULT_PAGE
	},
	totalItems: {
		type: Number,
		default: 0
	},
	perPage: {
		type: Number,
		default: DEFAULT_SERVER_PER_PAGE
	},
	lastPage: {
		type: Number,
		default: DEFAULT_LAST_PAGE
	},
	loading: {
		type: Boolean,
		default: false
	},
	emptyMessage: {
		type: String,
		default: DEFAULT_EMPTY_MESSAGE
	},
	infiniteScroll: {
		type: Boolean,
		default: false
	},
	hasMore: {
		type: Boolean,
		default: false
	},
	loadingMore: {
		type: Boolean,
		default: false
	},
	/** Rows before end of loaded list where next page is requested (e.g. 10 → trigger near row 40 of 50). */
	loadMoreOffset: {
		type: Number,
		default: 10
	},
	scrollMaxHeight: {
		type: String,
		default: 'calc(100vh - 320px)'
	}
})

const SCROLL_LOAD_MARGIN_PX = 120

const scrollContainerRef = ref(null)
const sentinelEl = ref(null)
let infiniteScrollObserver = null
let loadMoreEmitted = false

const bindSentinelRef = (el) => {
	sentinelEl.value = el
}

const scrollContainerStyle = computed(() => {
	if (!props.infiniteScroll) {
		return undefined
	}
	return {
		maxHeight: props.scrollMaxHeight,
		overflowY: 'auto',
		overflowX: 'auto',
	}
})

const getSentinelElement = () => sentinelEl.value || null

const isDarkMode = ref(false)

const checkTheme = () => {
	if (typeof document !== 'undefined') {
		isDarkMode.value = document.documentElement.hasAttribute('data-theme') &&
			document.documentElement.getAttribute('data-theme') === 'dark'
	}
}

const observer = new MutationObserver(() => {
	checkTheme()
})

const searchTerm = ref('')
const localCurrentPage = ref(DEFAULT_PAGE)
const localPageSize = ref(props.defaultPageSize)
const sortKey = ref(null)
const sortDirection = ref(DEFAULT_SORT_DIRECTION)
const indexKey = INDEX_KEY

const currentPage = computed(() => props.serverPagination ? props.currentPage : localCurrentPage.value)
const pageSize = computed(() => {
	if (props.serverPagination) {
		return props.perPage || DEFAULT_SERVER_PER_PAGE
	}
	return localPageSize.value
})

const allRows = computed(() =>
	Array.isArray(props.rows) ? props.rows : []
)

const filteredRows = computed(() => {
	if (!props.showSearchControl || !searchTerm.value) {
		return allRows.value
	}
	const term = searchTerm.value.toLowerCase()
	return allRows.value.filter((row) =>
		Object.values(row ?? {}).some((v) =>
			String(v ?? '').toLowerCase().includes(term)
		)
	)
})

const sortedRows = computed(() => {
	if (!props.sortable || !sortKey.value) {
		return filteredRows.value
	}
	const key = sortKey.value
	const dir = sortDirection.value === SORT_DIRECTION_ASC ? 1 : -1

	return [...filteredRows.value].sort((a, b) => {
		if (key === indexKey) {
			// sort by original index in array
			const ai = allRows.value.indexOf(a)
			const bi = allRows.value.indexOf(b)
			return (ai - bi) * dir
		}
		const av = a?.[key]
		const bv = b?.[key]

		if (av == null && bv == null) return 0
		if (av == null) return -1 * dir
		if (bv == null) return 1 * dir

		if (typeof av === 'number' && typeof bv === 'number') {
			return (av - bv) * dir
		}

		return String(av).localeCompare(String(bv)) * dir
	})
})

const totalEntries = computed(() => 
	props.serverPagination ? props.totalItems : sortedRows.value.length
)
const totalPages = computed(() => {
	if (props.serverPagination) {
		return props.lastPage || DEFAULT_LAST_PAGE
	}
	return totalEntries.value === 0 ? DEFAULT_LAST_PAGE : Math.ceil(totalEntries.value / pageSize.value)
})

const paginatedRows = computed(() => {
	if (props.serverPagination) {
		return sortedRows.value
	}
	const start = (currentPage.value - 1) * pageSize.value
	return sortedRows.value.slice(start, start + pageSize.value)
})

const startEntry = computed(() => {
	if (totalEntries.value === 0) return 0
	return (currentPage.value - 1) * pageSize.value + 1
})
const endEntry = computed(() => {
	if (props.serverPagination) {
		return Math.min(currentPage.value * pageSize.value, totalEntries.value)
	}
	return Math.min(currentPage.value * pageSize.value, totalEntries.value)
})

const loadedCount = computed(() => {
	if (props.infiniteScroll) {
		return allRows.value.length
	}
	return endEntry.value
})

/** Index of row after which the load-more sentinel is inserted (e.g. row ~40 when 50 loaded, offset 10). */
const loadMoreTriggerIndex = computed(() => {
	const count = paginatedRows.value.length
	if (count <= 0) {
		return -1
	}
	const offset = Math.max(1, props.loadMoreOffset)
	if (count <= offset) {
		return count - 1
	}
	return count - offset
})

const tryLoadMore = () => {
	if (
		loadMoreEmitted
		|| !props.infiniteScroll
		|| !props.hasMore
		|| props.loading
		|| props.loadingMore
	) {
		return
	}
	loadMoreEmitted = true
	emit('load-more')
}

const checkInfiniteScrollProximity = () => {
	if (!props.infiniteScroll || !props.hasMore || props.loading || props.loadingMore) {
		return false
	}

	const root = scrollContainerRef.value
	const sentinel = getSentinelElement()
	if (!root || !sentinel) {
		return false
	}

	const rootRect = root.getBoundingClientRect()
	const sentinelRect = sentinel.getBoundingClientRect()

	if (sentinelRect.top <= rootRect.bottom + SCROLL_LOAD_MARGIN_PX) {
		tryLoadMore()
		return true
	}
	return false
}

const fillScrollContainerIfNeeded = () => {
	const root = scrollContainerRef.value
	if (!root || !props.infiniteScroll || !props.hasMore || props.loading || props.loadingMore) {
		return
	}
	if (root.scrollHeight <= root.clientHeight + 4) {
		tryLoadMore()
	}
}

const onScrollContainer = () => {
	checkInfiniteScrollProximity()
}

const setupInfiniteScrollObserver = () => {
	teardownInfiniteScrollObserver()
	if (!props.infiniteScroll || !props.hasMore) {
		return
	}

	const root = scrollContainerRef.value
	const target = getSentinelElement()
	if (!root || !target) {
		return
	}

	infiniteScrollObserver = new IntersectionObserver(
		(entries) => {
			if (entries.some((entry) => entry.isIntersecting)) {
				tryLoadMore()
			}
		},
		{
			root,
			rootMargin: `0px 0px ${SCROLL_LOAD_MARGIN_PX}px 0px`,
			threshold: 0,
		}
	)
	infiniteScrollObserver.observe(target)
}

const refreshInfiniteScroll = () => {
	nextTick(() => {
		setupInfiniteScrollObserver()
		checkInfiniteScrollProximity()
		fillScrollContainerIfNeeded()
	})
}

watch(() => props.loadingMore, (loadingMore) => {
	if (!loadingMore) {
		loadMoreEmitted = false
		refreshInfiniteScroll()
	}
})

watch(
	() => [
		props.infiniteScroll,
		props.rows.length,
		props.hasMore,
		props.loading,
		loadMoreTriggerIndex.value,
	],
	() => {
		refreshInfiniteScroll()
	}
)

onMounted(() => {
	checkTheme()
	observer.observe(document.documentElement, {
		attributes: true,
		attributeFilter: ['data-theme']
	})
	refreshInfiniteScroll()
})

onUnmounted(() => {
	observer.disconnect()
	teardownInfiniteScrollObserver()
})

const teardownInfiniteScrollObserver = () => {
	if (infiniteScrollObserver) {
		infiniteScrollObserver.disconnect()
		infiniteScrollObserver = null
	}
}

const pages = computed(() => {
	const total = totalPages.value
	if (total <= MAX_PAGES_TO_SHOW_ALL) {
		return Array.from({ length: total }, (_, i) => i + 1)
	}
	
	const current = currentPage.value
	const pages = []
	
	pages.push(DEFAULT_PAGE)
	
	if (current > PAGES_ELLIPSIS_THRESHOLD) {
		pages.push(PAGES_ELLIPSIS)
	}
	
	const start = Math.max(2, current - PAGES_AROUND_CURRENT)
	const end = Math.min(total - 1, current + PAGES_AROUND_CURRENT)
	
	for (let i = start; i <= end; i++) {
		if (!pages.includes(i)) {
			pages.push(i)
		}
	}
	
	if (current < total - 2) {
		pages.push(PAGES_ELLIPSIS)
	}
	
	if (!pages.includes(total)) {
		pages.push(total)
	}
	
	return pages
})

watch(() => pageSize.value, (newSize) => {
	if (!props.serverPagination) {
		localCurrentPage.value = 1
		emit('update:page-size', newSize)
	}
})

watch(() => props.rows, () => {
	if (!props.serverPagination) {
		localCurrentPage.value = 1
	}
}, { deep: true })

watch(() => props.currentPage, (newPage) => {
	if (props.serverPagination) {
		localCurrentPage.value = newPage
	}
})

watch(() => props.perPage, (newPerPage) => {
	if (props.serverPagination) {
		localPageSize.value = newPerPage
	}
})

const isSortedIndex = computed(() =>
	sortKey.value === indexKey
)

const isColumnSortable = (column) =>
	props.sortable && column && column.sortable !== false

const headerClass = (column) => {
	const classes = []
	
	if (column.class) {
		classes.push(column.class)
	}
	
	if (!isColumnSortable(column)) {
		return classes.join(' ')
	}
	const isActive = sortKey.value === column.key
	const sortClasses = {
		[CSS_CLASS_ORDERABLE_ASC]: true,
		[CSS_CLASS_ORDERABLE_DESC]: true,
		[CSS_CLASS_ORDERING_ASC]: isActive && sortDirection.value === SORT_DIRECTION_ASC,
		[CSS_CLASS_ORDERING_DESC]: isActive && sortDirection.value === SORT_DIRECTION_DESC
	}
	
	return [
		...classes,
		Object.keys(sortClasses).filter(key => sortClasses[key]).join(' ')
	].filter(Boolean).join(' ')
}

const sortIconClass = (key) => {
	if (!props.sortable) {
		return SORT_ICON_DEFAULT
	}
	if (sortKey.value !== key) {
		return SORT_ICON_DEFAULT
	}
	return sortDirection.value === SORT_DIRECTION_ASC ? SORT_ICON_ASC : SORT_ICON_DESC
}

const toggleSort = (key) => {
	if (!props.sortable || !key) return

	if (sortKey.value === key) {
		sortDirection.value = sortDirection.value === SORT_DIRECTION_ASC ? SORT_DIRECTION_DESC : SORT_DIRECTION_ASC
	} else {
		sortKey.value = key
		sortDirection.value = SORT_DIRECTION_ASC
	}
}

const goToPage = (page) => {
	if (page < 1 || page > totalPages.value) return
	
	if (props.serverPagination) {
		// Emit event for parent to handle server-side pagination
		emit('page-change', page)
	} else {
		// Client-side pagination
		localCurrentPage.value = page
	}
}

const globalIndex = (localIndex) =>
	(currentPage.value - 1) * pageSize.value + localIndex + 1

const onHeaderClick = (column) => {
	if (!isColumnSortable(column)) return
	toggleSort(column.key)
}

const handlePerPageChange = (event) => {
	const newSize = parseInt(event.target.value, 10)
	
	if (isNaN(newSize) || newSize < 1) {
		return
	}
	
	if (props.serverPagination) {
		emit('per-page-change', newSize)
	} else {
		localPageSize.value = newSize
		localCurrentPage.value = 1
		emit('update:page-size', newSize)
	}
}

const totalColspan = computed(() => {
	let cols = props.columns.length
	if (props.showIndex) {
		cols += 1
	}
	return cols
})
</script>

<style scoped>
</style>


