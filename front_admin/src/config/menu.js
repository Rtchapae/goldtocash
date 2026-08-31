export const adminMenu = [
	{
		title: 'Dashboard',
		icon: 'solar:home-smile-angle-outline', // fa-tachometer-alt
		route: 'dashboard',
		type: 'link'
	},
	{
		title: 'Users',
		icon: 'solar:user-outline', // fa-user
		route: 'users.list',
		type: 'link'
	},
	{
		title: 'Admin Users',
		icon: 'solar:users-group-two-rounded-outline', // fa-users-cog
		route: 'admin-users.list',
		type: 'link'
	},
	{
		title: 'Active Orders',
		icon: 'solar:box-outline', // fa-box-open
		route: 'orders.list',
		type: 'link',
		badge: {
			key: 'appraisalRequests',
			class: 'badge-warning'
		}
	},
	{
		title: 'Pending Offers',
		icon: 'solar:scale-outline', // fa-balance-scale
		route: 'orders.pending',
		type: 'link',
		badge: {
			key: 'offersMade',
			class: 'badge-warning'
		}
	},
	{
		title: 'Payment History',
		icon: 'solar:history-outline', // fa-history
		route: 'orders.paid',
		type: 'link'
	},
	{
		title: 'Offline Transactions',
		icon: 'solar:shop-outline', // fa-store
		route: 'orders.offline',
		type: 'link'
	},
	{
		title: 'Sms',
		icon: 'solar:chat-round-outline', // fa-sms
		route: 'sms.index',
		type: 'link'
	},
	{
		title: 'Blog Posts',
		icon: 'solar:document-text-outline', // fa-scroll
		route: 'posts.index',
		type: 'link'
	},
	{
		title: 'Page Builder',
		icon: 'solar:widget-outline',
		route: 'builder-pages.index',
		type: 'link'
	},
	{
		title: 'Trace Events',
		icon: 'solar:pen-outline', // fa-pen
		route: 'trace.events',
		type: 'link'
	},
	{
		title: 'Trace Conversions',
		icon: 'mdi:percent', // fa-percent
		route: 'trace.conversions',
		type: 'link'
	},
	{
		title: 'Campaigns Stats',
		icon: 'solar:chart-outline', // fa-chart-line
		route: 'trace.campaign-stats',
		type: 'link'
	},
	{
		title: 'SEO Pages',
		icon: 'solar:document-text-outline', // fa-file-alt
		route: 'seo-pages.index',
		type: 'link'
	}
]

