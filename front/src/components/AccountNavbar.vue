<template>
	<nav class="navbar navbar-expand-lg navbar-dark" :class="{ 'navbar--with-banner-offset': !isScrolled }">
		<!-- Mobile menu toggle -->
		<button
			class="navbar-toggler"
			type="button"
			data-bs-toggle="collapse"
			data-bs-target="#navbarResponsive"
			aria-controls="navbarResponsive"
			:aria-expanded="isMenuOpen"
			aria-label="Toggle navigation"
			@click.prevent="isMenuOpen = !isMenuOpen"
		>
			<span>
				<img src="/images/menu-icon.svg" alt="Icon">
			</span>
		</button>

		<!-- Logo -->
		<a class="navbar-brand" href="/">
			<img src="/images/logo/white.svg" alt="Gold to Cash" width="240">
		</a>

		<div class="navbar-phone">
			<a href="tel:5642377332">
				<img src="/images/phone-call-white-icon.svg" alt="Icon">
			</a>
		</div>

		<!-- Collapsible menu -->
		<div
			class="collapse navbar-collapse"
			:class="{ show: isMenuOpen }"
			id="navbarResponsive"
		>
			<button
				class="navbar-toggler closeMenu"
				type="button"
				data-bs-toggle="collapse"
				data-bs-target="#navbarResponsive"
				aria-controls="navbarResponsive"
				:aria-expanded="isMenuOpen"
				aria-label="Toggle navigation"
				@click.prevent="isMenuOpen = false"
			>
				<span>
					<img src="/images/close-icon.svg" alt="Icon">
				</span>
			</button>

			<ul class="navbar-nav ms-auto align-items-lg-center">
				<button
					type="button"
					class="btn btn-primary d-lg-none"
					@click.prevent="handleRequestKit"
				>
					Request Free Kit
				</button>

				<li class="nav-item">
					<a href="/">Home</a>
				</li>
				<li class="nav-item">
					<a href="/user/account">Dashboard</a>
				</li>

				<p class="getInTouch d-lg-none">
					<img src="/images/phone-call-icon.svg" alt="Icon">
					<a href="tel:5642377332">(564) 237-7332</a><br>
					<span>(Mon - Fri, 9am - 5pm PST)</span>
				</p>
				<p class="getInTouch d-lg-none">
					<img src="/images/mail-icon.svg" alt="Icon">
					<a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a>
				</p>
			</ul>
		</div>

		<a
			class="d-none d-lg-block btn-secondary"
			href="#"
			@click.prevent="handleRequestKit"
		>
			Request Free Kit
		</a>
	</nav>
</template>

<script setup>
import { inject, ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'

const isMenuOpen = ref(false)

const openKitModal = inject('openKitModal', null)
const openAccountKitRequest = inject('openAccountKitRequest', null)

const route = useRoute()
const isAccountPage = route.path.startsWith('/user')

const isScrolled = ref(false)
let handleScroll = null

const handleRequestKit = () => {
	isMenuOpen.value = false

	if (isAccountPage && openAccountKitRequest?.value) {
		openAccountKitRequest.value()
	} else if (typeof openKitModal === 'function') {
		openKitModal()
	}
}

onMounted(() => {
	handleScroll = () => {
		isScrolled.value = window.scrollY >= 1
	}
	handleScroll()
	window.addEventListener('scroll', handleScroll, { passive: true })
	window.addEventListener('resize', handleScroll, { passive: true })
})

onBeforeUnmount(() => {
	if (handleScroll) {
		window.removeEventListener('scroll', handleScroll)
		window.removeEventListener('resize', handleScroll)
	}
})
</script>

<style scoped>
</style>
