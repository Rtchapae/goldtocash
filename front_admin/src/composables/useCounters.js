import { onMounted } from 'vue'
import { getCounters } from '@/api/adminCounters'
import { useCountersStore } from '@/stores/counters'

export const useCounters = () => {
	const countersStore = useCountersStore()

	const loadCounters = async (options = {}) => {
		try {
			const response = await getCounters()
			if (response.status && response.counters) {
				countersStore.setCounters(response.counters)
			}
		} catch (error) {
			console.error('Failed to load counters:', error)
		}
	}

	const initCounters = () => {
		onMounted(() => {
			loadCounters()
		})
	}

	return {
		loadCounters,
		initCounters,
	}
}

