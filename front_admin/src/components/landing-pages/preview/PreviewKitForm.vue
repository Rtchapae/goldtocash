<template>
	<div class="lp-kit-form" :class="`lp-kit-form--${data.layout || 'centered'}`">
		<h3 v-if="data.title" class="lp-kit-form__title">{{ data.title }}</h3>
		<div class="lp-kit-form__placeholder">
			<i class="fas fa-clipboard-list me-2"></i>
			Kit request form
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
