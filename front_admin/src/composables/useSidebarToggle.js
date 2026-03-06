import { ref, watch, nextTick } from 'vue'

const STORAGE_KEY = 'sidebar-collapsed'

const isDesktopCollapsed = ref(false)
const isMobileOpen = ref(false)
let initialized = false

const loadState = () => {
	if (typeof window !== 'undefined') {
		const saved = localStorage.getItem(STORAGE_KEY)
		if (saved !== null) {
			isDesktopCollapsed.value = saved === 'true'
		}
	}
}

const saveState = () => {
	if (typeof window !== 'undefined') {
		localStorage.setItem(STORAGE_KEY, String(isDesktopCollapsed.value))
	}
}

const applyDesktopClasses = () => {
	if (typeof window === 'undefined') return

	nextTick(() => {
		const sidebarToggle = document.querySelector('.sidebar-toggle')
		const sidebar = document.querySelector('.sidebar')
		const dashboardMain = document.querySelector('.dashboard-main')

		if (sidebarToggle) {
			if (isDesktopCollapsed.value) {
				sidebarToggle.classList.add('active')
			} else {
				sidebarToggle.classList.remove('active')
			}
		}

		if (sidebar) {
			if (isDesktopCollapsed.value) {
				sidebar.classList.add('active')
			} else {
				sidebar.classList.remove('active')
			}
		}

		if (dashboardMain) {
			if (isDesktopCollapsed.value) {
				dashboardMain.classList.add('active')
			} else {
				dashboardMain.classList.remove('active')
			}
		}
	})
}

const applyMobileClasses = () => {
	if (typeof window === 'undefined') return

	nextTick(() => {
		const sidebar = document.querySelector('.sidebar')
		const body = document.body

		if (sidebar) {
			if (isMobileOpen.value) {
				sidebar.classList.add('sidebar-open')
			} else {
				sidebar.classList.remove('sidebar-open')
			}
		}

		if (body) {
			if (isMobileOpen.value) {
				body.classList.add('overlay-active')
			} else {
				body.classList.remove('overlay-active')
			}
		}
	})
}

const toggleDesktop = () => {
	isDesktopCollapsed.value = !isDesktopCollapsed.value
	saveState()
	applyDesktopClasses()
}

const toggleMobile = () => {
	isMobileOpen.value = !isMobileOpen.value
	applyMobileClasses()
}

const closeMobile = () => {
	isMobileOpen.value = false
	applyMobileClasses()
}

const initialize = () => {
	if (initialized) return
	initialized = true
	
	loadState()
	
	if (typeof window !== 'undefined') {
		nextTick(() => {
			applyDesktopClasses()
			applyMobileClasses()
		})
	}
	
	watch(isDesktopCollapsed, () => {
		applyDesktopClasses()
	})
	
	watch(isMobileOpen, () => {
		applyMobileClasses()
	})
}

export const useSidebarToggle = () => {
	initialize()
	
	return {
		isDesktopCollapsed,
		isMobileOpen,
		toggleDesktop,
		toggleMobile,
		closeMobile
	}
}

