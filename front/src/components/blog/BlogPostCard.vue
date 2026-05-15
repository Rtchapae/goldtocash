<template>
	<div :class="wrapperClass">
		<div class="card my-2">
			<div class="card-body">
				<div class="image-link">
					<a :href="articleUrl">
						<img class="image" :key="post.image" :src="post.image" :alt="post.title">
					</a>
				</div>
				<div class="card-section p-2 h4">
					<a class="font-bold" :href="articleUrl">{{ post.title }}</a>
					<p>{{ post.excerpt }}</p>
				</div>
				<div class="d-flex justify-content-between">
					<div class="small">{{ post.date }}</div>
					<a class="btn btn-gold" :href="articleUrl">
						Read More <span>→</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	post: {
		type: Object,
		required: true
	},
	basePath: {
		type: String,
		default: '/sell-gold'
	},
	columnClass: {
		type: String,
		default: 'col-lg-4'
	}
})

const wrapperClass = computed(() => `${props.columnClass} d-flex align-items-sm-stretch`)

const getArticleUrl = (slug) => {
	return `${props.basePath}/${slug}`
}

const articleUrl = computed(() => getArticleUrl(props.post.slug))
</script>

<style scoped>
.image-link {
	position: relative;
	overflow: hidden;
}

.image {
	width: 100%;
	height: auto;
	display: block;
}

.card-section a {
	text-decoration: none;
	color: inherit;
}

.card-section a:hover {
	color: var(--primary);
}

.card {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
div.h4 p {
    margin-top: 10px;
}
.image {
    width: 100%;
    height: -webkit-fill-available;
    object-fit: cover;
}
.image-link {
    position: relative;
    background: #ccc;
    border: 1px solid #ccc;
}
.btn-gold {
    color: #212529;
    background-color: #dbaf3e;
    border-color: #dbaf3e;
    float: right;
}
.col-lg-4 div.card-box {
    height: 520px;

}
.col-lg-4 img,
.col-lg-4 div.image-link {
    height: 300px;
}
.col-lg-3 div.card-box {
    height: 450px;
}
.col-lg-3 img,
.col-lg-3 div.image-link {
    height: 200px;
}
.col-lg-3 div.h4 {
    font-size: 1.2rem;
}
.btn-block {
    bottom: 15px;
    position: absolute;
    left: 30px;
}
.blog-head {
    margin-top: 150px;
    margin-bottom: 50px;
}
@media(max-width:767px) {
    .blog-head {
        margin-top: 75px;
        padding: 0 .5rem;
    }
}
.blog-content {
    margin-bottom: 200px;
}
.page-item.active .page-link {
    color: #212529;
    background-color: #dbaf3e;
    border-color: #dbaf3e;
}
</style>

