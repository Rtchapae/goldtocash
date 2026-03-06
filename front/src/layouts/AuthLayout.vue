<template>
	<div>
		<Preloader :show="showPreloader" />
		<div class="app-shell" :class="{ 'is-hidden': showPreloader }">
			<div class="container-scroller">
				<div class="container-fluid page-body-wrapper full-page-wrapper">
					<div class="content-wrapper-login align-items-center auth theme-one">
						<AuthNavbar />

						<main>
							<slot />
						</main>

						<AuthFooter />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Preloader from '@/components/Preloader.vue'
import AuthNavbar from '@/components/auth/AuthNavbar.vue'
import AuthFooter from '@/components/auth/AuthFooter.vue'

const showPreloader = ref(true)

onMounted(() => {
	setTimeout(() => {
		showPreloader.value = false
	}, 500)
})
</script>

<style scoped>
.app-shell.is-hidden {
	visibility: hidden;
}


</style>
