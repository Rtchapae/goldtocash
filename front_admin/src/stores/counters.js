import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCountersStore = defineStore('counters', () => {
	const counters = ref({
		appraisalRequests: 0,
		offersMade: 0,
	})

	const setCounters = (newCounters, options = {}) => {
		counters.value = { ...counters.value, ...newCounters }
	}

	const updateCounter = (key, value) => {
		counters.value = { ...counters.value, [key]: value }
	}

	return {
		counters,
		setCounters,
		updateCounter
	}
})




