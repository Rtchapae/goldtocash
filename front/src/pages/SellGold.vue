<template>
	<section class="section-blog">
		<div class="container page-content">
			<div class="row justify-content-center">
				<div class="col-12">
					<div class="column text-center blog-head">
						<h1 class="font-heavy">Gold News</h1>
						<h2 class="font-bold font-primary">The Latest News on Gold, Jewelry, and Diamonds</h2>

						<div>
							<a class="btn btn-light" href="/sell-gold">
								<img width="20" src="/images/grid-small.svg" alt="Grid small">
							</a>
							<a class="btn btn-light" href="/sell-gold?small=yes">
								<img width="20" src="/images/grid-large.svg" alt="Grid large">
							</a>
						</div>
					</div>
				</div>

				<div class="col-lg-10 col-xl-12 d-flex flex-wrap blog-content">
					<BlogPostCard
						v-for="post in posts"
						:key="post.slug"
						:post="post"
						base-path="/sell-gold"
						:column-class="gridColumnClass"
					/>
				</div>
			</div>
			<BlogPagination :current-page="currentPage" :total-pages="totalPages" base-path="/sell-gold" />
		</div>
	</section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import BlogPostCard from '@/components/blog/BlogPostCard.vue'
import BlogPagination from '@/components/blog/BlogPagination.vue'
import { fetchPosts } from '@/api/posts'

const route = useRoute()

const posts = ref([])
const isLoading = ref(false)
const error = ref(null)
const pagination = ref({
	current_page: 1,
	last_page: 1,
	per_page: 15,
	total: 0
})

const currentPage = computed(() => {
	return parseInt(route.query.page) || 1
})

const totalPages = computed(() => {
	return pagination.value.last_page || 1
})

const gridColumnClass = computed(() => {
	// Default: 3 columns; ?small=yes switches to 2 larger cards (grid toggle)
	const isTwoColumns = route.query.small === 'yes'
	return isTwoColumns ? 'col-lg-6' : 'col-lg-4'
})

const loadPosts = async () => {
	isLoading.value = true
	error.value = null
	
	try {
		const response = await fetchPosts({
			path_prefix: '/sell-gold',
			page: currentPage.value,
			per_page: 15
		})
		
		if (response.data && Array.isArray(response.data)) {
			posts.value = response.data
		}
		
		if (response.meta) {
			pagination.value = response.meta
		}
	} catch (err) {
		error.value = err.message || 'Failed to load posts'
		console.error('Failed to load posts:', err)
	} finally {
		isLoading.value = false
	}
}

onMounted(() => {
	loadPosts()
})

watch(() => route.query.page, () => {
	loadPosts()
})
</script>

<style scoped>
.page-content {
	padding: 150px 0 0;
}

.blog-head {
	margin-bottom: 3rem;
}

.blog-head h1 {
	font-size: 3rem;
	margin-bottom: 1rem;
}

.blog-head h2 {
	font-size: 1.5rem;
	margin-bottom: 2rem;
}

.blog-content {
	margin-bottom: 3rem;
}

@media (min-width: 1200px) {
	.page-content .container {
		max-width: 1400px;
	}
}

@media (min-width: 1400px) {
	.page-content .container {
		max-width: 1600px;
	}
}

</style>
