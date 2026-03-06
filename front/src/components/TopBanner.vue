<template>
	<section class="section-banner" :class="bannerClasses">
		<div class="row align-items-center">
			<div class="col-auto">
				<a href="tel:5642377332">
					<img src="/images/phone-call-white-icon.svg" alt="Icon">
					<span class="d-none d-lg-block">(564) 237-7332</span>
				</a>
			</div>
			<div class="col d-flex justify-content-center">
				<p v-if="isAccountPage" class="d-none d-lg-block">
					{{ userName ? `${userName}, welcome to your Account.` : 'Welcome to your Account.' }}
				</p>
				<p v-else class="d-none d-lg-block">
					You will get your items back SAFE &amp; FREE if not satisfied.
				</p>

				<p v-if="isAccountPage" class="d-lg-none">
					{{ userName ? `${userName}, welcome!` : 'Welcome to your Account.' }}
				</p>
				<p v-else class="d-lg-none">Satisfaction Guaranteed!</p>
			</div>
			<div class="col-2">
				<a v-if="isAccountPage" class="mr-3 float-right" href="#" @click.prevent="handleLogout">
					<i class="fas fa-sign-out-alt mr-2"></i>
					<span class="d-none d-lg-block">Logout</span>
				</a>

				<a v-else class="mr-3 float-right" href="/sign-in">
					<span class="d-none d-lg-block">Sign In</span>
					<span class="d-none d-sm-block d-md-none">
						<img src="/images/user-icon-white.svg" alt="Icon">
					</span>
					<span class="d-block d-sm-none">
						<img src="/images/user-icon.svg" alt="Icon">
					</span>
					<span class="d-none d-md-block d-lg-none">
						<img src="/images/user-icon-white.svg" alt="Icon">
					</span>
				</a>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { logout } from '@/api/auth'
import { useToast } from '@/composables/useToast'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const toast = useToast()

const isHome = computed(() => route.path === '/')
const isAccountPage = computed(() => route.path.startsWith('/user'))

const userName = computed(() => {
	if (route.query.name) {
		return route.query.name
	}
	
	if (userStore.profile) {
		const name = userStore.profile.name?.trim()
		if (name) return name
		
		const firstName = userStore.profile.first_name?.trim()
		if (firstName) return firstName
		
		const email = userStore.profile.email?.trim()
		if (email) return email.split('@')[0]
	}
	
	return null
})

const handleLogout = async () => {
	try {
		await logout()
		toast.success('Logged out successfully')
	} catch (_) {
		toast.info('Logged out')
	} finally {
		userStore.setProfile(null)
		setTimeout(() => {
			router.push('/sign-in')
		}, 500)
	}
}

const bannerClasses = computed(() => ({
	'banner--transparent': isHome.value,
	'd-md-block': !isAccountPage.value,
}))
</script>

<style scoped>
</style>


