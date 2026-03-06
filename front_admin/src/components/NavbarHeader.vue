<template>
	<div class="navbar-header">
		<div class="row align-items-center justify-content-between">
			<div class="col-auto">
				<div class="d-flex flex-wrap align-items-center gap-4">
					<button 
						type="button" 
						class="sidebar-toggle"
						:class="{ active: isDesktopCollapsed }"
						@click="toggleDesktop"
					>
						<iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
						<iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
					</button>
					<button 
						type="button" 
						class="sidebar-mobile-toggle"
						@click="toggleMobile"
					>
						<iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
					</button>
					<form class="navbar-search">
						<input type="text" name="search" placeholder="Search" />
						<iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
					</form>
				</div>
			</div>
			<div class="col-auto">
				<div class="d-flex flex-wrap align-items-center gap-3">
					<button
						type="button"
						@click="toggleTheme"
						:aria-label="isDarkTheme ? 'Switch to light mode' : 'Switch to dark mode'"
						class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center theme-toggle-btn"
						:class="{ 'theme-dark': isDarkTheme }"
					>
						<iconify-icon
							:icon="isDarkTheme ? 'solar:moon-bold' : 'solar:sun-bold'"
							class="text-xl text-primary-light"
						></iconify-icon>
					</button>

					<div class="dropdown">
						<button
							class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center position-relative"
							type="button"
							data-bs-toggle="dropdown"
							@click="markNotificationsAsRead"
						>
							<iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
							<span
								v-if="unreadNotificationsCount > 0"
								class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
								style="font-size: 0.65rem; padding: 0.2rem 0.4rem;"
							>
								{{ unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount }}
							</span>
						</button>
						<div class="dropdown-menu to-top dropdown-menu-lg p-0">
							<div class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
								<div>
									<h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
								</div>
								<span class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">
									{{ totalNotificationsCount }}
								</span>
							</div>
							<div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
								<div v-if="notificationsLoading" class="text-center py-4">
									<div class="spinner-border spinner-border-sm text-primary" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
									<p class="text-muted mt-2 mb-0 small">Loading notifications...</p>
								</div>

								<template v-else-if="notifications.length > 0">
									<div
										v-for="notification in notifications"
										:key="notification.id"
										class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between notification-item cursor-pointer"
										:class="{ 'bg-neutral-50': notification.is_read }"
										@click="handleNotificationClick(notification)"
									>
										<div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
											<span
												class="w-44-px h-44-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
												:class="getNotificationIconClass(notification.type)"
											>
												<iconify-icon
													v-if="notification.type === 'appraisal_request'"
													icon="solar:document-bold"
													class="icon text-xl"
												></iconify-icon>
												<iconify-icon
													v-else-if="notification.type === 'offer_response'"
													icon="solar:dollar-bold"
													class="icon text-xl"
												></iconify-icon>
												<iconify-icon
													v-else-if="notification.type === 'status_change'"
													icon="solar:refresh-bold"
													class="icon text-xl"
												></iconify-icon>
												<iconify-icon
													v-else-if="notification.type === 'offline_transaction'"
													icon="solar:card-bold"
													class="icon text-xl"
												></iconify-icon>
												<img
													v-else-if="notification.user_avatar"
													:src="notification.user_avatar"
													alt=""
													class="w-100 h-100 object-fit-cover rounded-circle"
												/>
												<span v-else class="text-sm fw-semibold">
													{{ getUserInitials(notification.user_name) }}
												</span>
											</span>
											<div class="flex-grow-1">
												<h6 class="text-md fw-semibold mb-1">{{ notification.title }}</h6>
												<p class="mb-0 text-sm text-secondary-light text-w-200-px">{{ notification.message }}</p>
												<small class="text-muted d-block mt-1">{{ formatNotificationTime(notification.created_at) }}</small>
											</div>
										</div>
										<div v-if="!notification.is_read" class="flex-shrink-0">
											<span class="w-8-px h-8-px bg-primary-main rounded-circle d-inline-block"></span>
										</div>
									</div>
								</template>

								<!-- Empty state -->
								<div v-else class="text-center py-5">
									<iconify-icon icon="solar:bell-off-bold" class="text-muted mb-3" style="font-size: 2.5rem;"></iconify-icon>
									<h6 class="text-muted mb-1">No notifications yet</h6>
									<p class="text-muted mb-0 small">You'll see updates here when they happen</p>
								</div>
							</div>

						</div>
					</div>
					<div class="dropdown">
						<button class="d-flex justify-content-center align-items-center rounded-circle" type="button" data-bs-toggle="dropdown">
							<img src="/images/user.png" alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle" />
						</button>
						<div class="dropdown-menu to-top dropdown-menu-sm">
							<div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
								<div>
									<h6 class="text-lg text-primary-light fw-semibold mb-2">{{ userName }}</h6>
									<span class="text-secondary-light fw-medium text-sm">{{ userRole }}</span>
								</div>
								<button 
									type="button" 
									class="hover-text-danger"
									@click="closeDropdown"
								>
									<iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
								</button>
							</div>
							<ul class="to-top-list">
								<li>
									<a 
										class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3" 
										href="javascript:void(0)"
										@click="handleLogout"
									>
										<iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log Out
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSidebarToggle } from '@/composables/useSidebarToggle'
import { logoutAdmin, getMe } from '@/api/adminAuth'
import { useAdminAuthStore } from '@/stores/adminAuth'
import { useCounters } from '@/composables/useCounters'
import { fetchNotifications, markNotificationsAsRead as markAsReadApi } from '@/api/notifications'

const router = useRouter()
const route = useRoute()
const authStore = useAdminAuthStore()
const { isDesktopCollapsed, toggleDesktop, toggleMobile } = useSidebarToggle()
const { loadCounters } = useCounters()

const isLoggingOut = ref(false)
const isLoadingUser = ref(false)

const notifications = ref([])
const notificationsLoading = ref(false)
const unreadNotificationsCount = ref(0)
const totalNotificationsCount = ref(0)

const isDarkTheme = ref(false)

const initializeTheme = () => {
	if (typeof window !== 'undefined') {
		const currentTheme = document.documentElement.getAttribute('data-theme')
		if (currentTheme === 'dark') {
			isDarkTheme.value = true
		} else {
			const savedTheme = localStorage.getItem('admin_theme')
			isDarkTheme.value = savedTheme === 'dark'
		}

		updateTheme()
	}
}

const updateTheme = () => {
	if (typeof window !== 'undefined') {
		const html = document.documentElement
		if (isDarkTheme.value) {
			html.setAttribute('data-theme', 'dark')
		} else {
			html.removeAttribute('data-theme')
		}
	}
}

const toggleTheme = () => {
	isDarkTheme.value = !isDarkTheme.value
	updateTheme()

	if (typeof window !== 'undefined') {
		localStorage.setItem('admin_theme', isDarkTheme.value ? 'dark' : 'light')
	}
}

watch(isDarkTheme, (newValue) => {
	if (typeof window !== 'undefined') {
		localStorage.setItem('admin_theme', newValue ? 'dark' : 'light')
	}
})

const userName = computed(() => authStore.userName)
const userRole = computed(() => authStore.userRole)

const loadNotifications = async () => {
	if (!authStore.isAuthenticated) return

	try {
		notificationsLoading.value = true
		const response = await fetchNotifications({ limit: 10 })
		notifications.value = response.data || []

		unreadNotificationsCount.value = notifications.value.filter(n => !n.is_read).length
		totalNotificationsCount.value = notifications.value.length
	} catch (error) {
		console.error('Failed to load notifications:', error)
	} finally {
		notificationsLoading.value = false
	}
}

const markNotificationsAsRead = async () => {
	if (unreadNotificationsCount.value === 0) return

	try {
		await markAsReadApi()
		notifications.value.forEach(notification => {
			notification.is_read = true
		})
		unreadNotificationsCount.value = 0
	} catch (error) {
		console.error('Failed to mark notifications as read:', error)
	}
}

const handleNotificationClick = (notification) => {
	if (!notification.is_read) {
		notification.is_read = true
		unreadNotificationsCount.value = Math.max(0, unreadNotificationsCount.value - 1)
	}

	switch (notification.type) {
		case 'appraisal_request':
			router.push(`/orders?search=${notification.order_number}`)
			break
		case 'offer_response':
			router.push(`/orders?search=${notification.order_number}`)
			break
		case 'status_change':
			router.push(`/orders?search=${notification.kit_number}`)
			break
		case 'offline_transaction':
			router.push('/orders/offline')
			break
		default:
			break
	}
}

const getNotificationIconClass = (type) => {
	switch (type) {
		case 'appraisal_request':
			return 'bg-primary-subtle text-primary-main'
		case 'offer_response':
			return 'bg-success-subtle text-success-main'
		case 'status_change':
			return 'bg-info-subtle text-info-main'
		case 'offline_transaction':
			return 'bg-warning-subtle text-warning-main'
		default:
			return 'bg-secondary-subtle text-secondary-main'
	}
}

const getUserInitials = (name) => {
	if (!name) return 'U'
	return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatNotificationTime = (timestamp) => {
	if (!timestamp) return ''

	const now = new Date()
	const notificationTime = new Date(timestamp)
	const diffMs = now - notificationTime
	const diffMins = Math.floor(diffMs / (1000 * 60))
	const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
	const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

	if (diffMins < 1) return 'Just now'
	if (diffMins < 60) return `${diffMins}m ago`
	if (diffHours < 24) return `${diffHours}h ago`
	if (diffDays < 7) return `${diffDays}d ago`

	return notificationTime.toLocaleDateString()
}

onMounted(async () => {
	initializeTheme()

	if (authStore.isAuthenticated && !authStore.user) {
		await loadUserData()
	}
	if (authStore.isAuthenticated) {
		await loadCounters()
		await loadNotifications()
	}
})

watch(() => route.name, (newRoute, oldRoute) => {
	if (authStore.isAuthenticated && newRoute && newRoute !== oldRoute) {
		loadCounters()
	}
})

const loadUserData = async () => {
	if (isLoadingUser.value) return
	
	isLoadingUser.value = true
	try {
		const userData = await getMe()
		authStore.setUser(userData)
	} catch (error) {
		console.error('Failed to load user data:', error)
	} finally {
		isLoadingUser.value = false
	}
}

const closeDropdown = () => {
	const dropdownElement = document.querySelector('.dropdown-menu.show')
	if (dropdownElement) {
		const toggleButton = dropdownElement.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]')
		if (toggleButton) {
			toggleButton.click()
		}
	}
}

const handleSettings = () => {
	closeDropdown()
}

const handleLogout = async () => {
	if (isLoggingOut.value) return

	isLoggingOut.value = true
	closeDropdown()

	try {
		await logoutAdmin()
		authStore.clearToken()
		await router.push({ name: 'admin.login' })
	} catch (error) {
		authStore.clearToken()
		await router.push({ name: 'admin.login' })
	} finally {
		isLoggingOut.value = false
	}
}
</script>

