<template>
	<aside class="sidebar" :class="{ active: isDesktopCollapsed, 'sidebar-open': isMobileOpen }">
		<button type="button" class="sidebar-close-btn" @click="closeMobile">
			<iconify-icon icon="radix-icons:cross-2" class="icon"></iconify-icon>
		</button>
		<div>
			<a href="index.html" class="sidebar-logo">
				<img src="/images/logo-black-with-gold-c.png" alt="Royal Element" class="light-logo" />
				<img src="/images/logo-white-with-gold-c.png" alt="Royal Element" class="dark-logo" />
				<img src="/images/logo-preview-white.png" alt="Royal Element" class="logo-icon" />
			</a>
		</div>
		<div class="sidebar-menu-area">
			<ul class="sidebar-menu" id="sidebar-menu">
				<li 
					v-for="item in menuItems" 
					:key="item.route || item.title"
					class="_dropdown" 
					:class="{ 'active-page': route.name === item.route }"
				>
					<router-link v-if="item.type === 'link'" :to="{ name: item.route }">
						<iconify-icon :icon="item.icon" class="menu-icon"></iconify-icon>
						<span>{{ item.title }}</span>
						<span
							v-if="item.badge"
							class="right badge"
							:class="getBadgeClass(item.badge.key)"
						>
							{{ getBadgeValue(item.badge.key) }}
						</span>
					</router-link>
				</li>
			</ul>
		</div>
	</aside>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import { adminMenu } from '../config/menu.js'
import { useCountersStore } from '../stores/counters.js'
import { useAdminAuthStore } from '../stores/adminAuth.js'
import { useSidebarToggle } from '@/composables/useSidebarToggle'

const route = useRoute()
const countersStore = useCountersStore()
const adminAuthStore = useAdminAuthStore()
const { isDesktopCollapsed, isMobileOpen, closeMobile } = useSidebarToggle()

const menuItems = computed(() => {
	const userRole = adminAuthStore.userRole

	if (userRole === 'manager') {
		return adminMenu.filter(item =>
			item.route === 'dashboard' || item.route === 'orders.offline'
		)
	}

	return adminMenu
})

const { counters } = storeToRefs(countersStore)

const getBadgeValue = (key) => {
	return counters.value[key] || 0
}

const getBadgeClass = (key) => {
	const value = getBadgeValue(key)
	return value > 0 ? 'bg-warning text-dark' : 'bg-success text-white'
}
</script>

