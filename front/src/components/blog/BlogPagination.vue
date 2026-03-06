<template>
	<div class="d-flex justify-content-center">
		<nav>
			<ul class="pagination">
				<li 
					:class="['page-item', { disabled: currentPage === 1 }]" 
					:aria-disabled="currentPage === 1" 
					aria-label="« Previous"
				>
					<a 
						v-if="currentPage > 1" 
						class="page-link" 
						:href="getPageUrl(currentPage - 1)" 
						aria-label="« Previous"
					>‹</a>
					<span v-else class="page-link" aria-hidden="true">‹</span>
				</li>

				<li 
					v-for="page in pages" 
					:key="page"
					:class="['page-item', { active: page === currentPage }]"
					:aria-current="page === currentPage ? 'page' : undefined"
				>
					<a v-if="page !== currentPage" class="page-link" :href="getPageUrl(page)">{{ page }}</a>
					<span v-else class="page-link">{{ page }}</span>
				</li>

				<li 
					:class="['page-item', { disabled: currentPage === totalPages }]"
				>
					<a 
						v-if="currentPage < totalPages" 
						class="page-link" 
						:href="getPageUrl(currentPage + 1)" 
						rel="next"
						aria-label="Next »"
					>›</a>
					<span v-else class="page-link" aria-hidden="true">›</span>
				</li>
			</ul>
		</nav>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	currentPage: {
		type: Number,
		default: 1
	},
	totalPages: {
		type: Number,
		required: true
	},
	basePath: {
		type: String,
		default: '/sell-gold'
	}
})

const getPageUrl = (page) => {
	return `${props.basePath}?page=${page}`
}

const pages = computed(() => {
	const result = []
	for (let i = 1; i <= props.totalPages; i++) {
		result.push(i)
	}
	return result
})
</script>

<style scoped>
.pagination {
	display: flex;
	list-style: none;
	padding: 0;
	margin: 0;
}

.page-item {
	margin: 0 2px;
}

.page-link {
	display: block;
	padding: 0.5rem 0.75rem;
	text-decoration: none;
	color: var(--primary);
	border: 1px solid #dee2e6;
	border-radius: 0.25rem;
	transition: all 0.2s;
}

.page-link:hover {
	background-color: #e9ecef;
	color: var(--primary);
}

.page-item.active .page-link {
	background-color: var(--primary);
	color: white;
	border-color: var(--primary);
}

.page-item.disabled .page-link {
	color: #6c757d;
	pointer-events: none;
	cursor: not-allowed;
	background-color: #fff;
	border-color: #dee2e6;
}
</style>

