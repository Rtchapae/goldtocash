<template>
	<div class="lp-kit-form" :class="`lp-kit-form--${data.layout || 'centered'}`">
		<h2 v-if="data.title" class="lp-kit-form__title">{{ data.title }}</h2>
		<KitForm inline />
	</div>
</template>

<script setup>
import { computed } from 'vue'
import KitForm from '@/components/forms/KitForm.vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
