<template>
	<section class="section-calculator">
		<div class="container ">
			<h1 class="text-center mb-5">Gold Calculator</h1>
		<ClientOnly>
			<ValueCalculator />
		</ClientOnly>
		</div>
	</section>
</template>

<script setup>
import ValueCalculator from '@/components/ValueCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'
</script>
