import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getDashboardAnalytics } from '@/api/adminCounters'

export const useAnalyticsStore = defineStore('analytics', () => {
    const analytics = ref({
        online: {
            visitors: 0,
            orders: 0,
            transactions: 0,
            paidAmount: 0
        },
        offline: {},
        offlineCombined: {
            transactions: 0,
            paidAmount: 0
        },
        allCombined: {
            transactions: 0,
            paidAmount: 0
        },
        charts: {
            online: {
                visitors: [],
                orders: [],
                paidAmount: []
            },
            offline: {},
            offlineCombined: {
                transactions: [],
                paidAmount: []
            }
        }
    })

    const isLoading = ref(false)

    const getAnalytics = async (params = {}) => {
        isLoading.value = true
        try {
            const response = await getDashboardAnalytics(params)
            if (response.data) {
                analytics.value = response.data
            }
        } catch (error) {
            console.error('Failed to load analytics:', error)
        } finally {
            isLoading.value = false
        }
    }

    return { analytics, isLoading, getAnalytics }
})