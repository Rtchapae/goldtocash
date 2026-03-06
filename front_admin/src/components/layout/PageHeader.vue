<template>
	<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
		<div class="d-flex align-items-center gap-2">
			<h6 class="fw-semibold mb-0">{{ title }}</h6>
			<div v-if="loading" class="spinner-border spinner-border-sm text-primary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
		<ul class="d-flex align-items-center gap-2">
			<li class="fw-medium">
				<router-link to="/" class="d-flex align-items-center gap-1 hover-text-primary">
					<iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
					Dashboard
				</router-link>
			</li>
			<li>-</li>
			<li class="fw-medium">{{ title }}</li>
		</ul>
	</div>
</template>

<script setup>
const props = defineProps({
	title: {
		type: String,
		required: true
	},
	loading: {
		type: Boolean,
		default: false
	}
});
</script>

<style scoped>
.spinner-border-sm {
	width: 1rem;
	height: 1rem;
	border-width: 0.15em;
}

.visually-hidden {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border-width: 0;
}
</style>





