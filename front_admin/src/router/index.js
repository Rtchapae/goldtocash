import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '@/pages/DashboardPage.vue';
import LoginPage from '@/pages/Auth/LoginPage.vue';
import UsersListPage from '@/pages/UsersListPage.vue';
import OrdersListPage from '@/pages/OrdersListPage.vue';
import OrdersPendingPage from '@/pages/OrdersPendingPage.vue';
import OfflineTransactionsPage from '@/pages/OfflineTransactionsPage.vue';
import PaymentHistoryPage from '@/pages/PaymentHistoryPage.vue';
import SmsIndexPage from '@/pages/SmsIndexPage.vue';
import PostsIndexPage from '@/pages/PostsIndexPage.vue';
import CreatePostPage from '@/pages/CreatePostPage.vue';
import ExpensesIndexPage from '@/pages/ExpensesIndexPage.vue';
import TraceEventsPage from '@/pages/TraceEventsPage.vue';
import TraceConversionsPage from '@/pages/TraceConversionsPage.vue';
import TraceCampaignStatsPage from '@/pages/TraceCampaignStatsPage.vue';
import AdminUsersPage from '@/pages/AdminUsersPage.vue';
import SeoPagesIndexPage from '@/pages/SeoPages/SeoPagesIndexPage.vue';

const routes = [
	{
		path: '/login',
		name: 'admin.login',
		component: LoginPage,
		meta: {
			layout: 'auth'
		}
	},
	{
		path: '/',
		name: 'dashboard',
		component: DashboardPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/users',
		name: 'users.list',
		component: UsersListPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/orders',
		name: 'orders.list',
		component: OrdersListPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/orders/pending',
		name: 'orders.pending',
		component: OrdersPendingPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/orders/offline',
		name: 'orders.offline',
		component: OfflineTransactionsPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/orders/paid',
		name: 'orders.paid',
		component: PaymentHistoryPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/sms',
		name: 'sms.index',
		component: SmsIndexPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/posts',
		name: 'posts.index',
		component: PostsIndexPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/posts/create',
		name: 'posts.create',
		component: CreatePostPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/posts/edit/:id',
		name: 'posts.edit',
		component: CreatePostPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/expenses',
		name: 'expenses.index',
		component: ExpensesIndexPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/trace/events',
		name: 'trace.events',
		component: TraceEventsPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/trace/conversions',
		name: 'trace.conversions',
		component: TraceConversionsPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/trace/campaign-stats',
		name: 'trace.campaign-stats',
		component: TraceCampaignStatsPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/admin-users',
		name: 'admin-users.list',
		component: AdminUsersPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/seo-pages',
		name: 'seo-pages.index',
		component: SeoPagesIndexPage,
		meta: { requiresAuth: true }
	},
	{
		path: '/orders/create',
		name: 'orders.create',
		component: () => import('@/pages/CreateOrderPage.vue'),
		meta: { requiresAuth: true }
	}
];

const router = createRouter({
	history: createWebHistory(),
	routes,
	linkActiveClass: 'active-page',
	linkExactActiveClass: 'active-page'
});

router.beforeEach((to, from, next) => {
	if (to.name === 'admin.login') {
		if (typeof window !== 'undefined') {
			const token = window.localStorage.getItem('admin_access_token')
			if (token) {
				next({ name: 'dashboard' })
				return
			}
		}
		next()
		return
	}

	if (to?.meta?.requiresAuth) {
		if (typeof window === 'undefined') {
			next()
			return
		}

		const token = window.localStorage.getItem('admin_access_token')

		if (!token) {
			next({
				name: 'admin.login',
				query: { redirect: to.fullPath }
			})
			return
		}

		try {
			const payload = JSON.parse(atob(token.split('.')[1]))
			const userRole = payload.role || 'admin'

			if (userRole === 'manager') {
				const allowedRoutes = ['dashboard', 'orders.offline']
				if (!allowedRoutes.includes(to.name)) {
					next({ name: 'dashboard' })
					return
				}
			}

			next()
		} catch (error) {
			next({ name: 'admin.login' })
		}
	} else {
		next()
	}
})

export default router

