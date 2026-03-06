import { ref, onMounted, onUnmounted } from 'vue'
import { getRecentPayout } from '@/api/recentPayout'

export function useRecentPayoutToast() {
  const isVisible = ref(false)
  const payoutData = ref(null)
  const wasClosed = ref(false)
  const intervalId = ref(null)
  const timeoutId = ref(null)

  const showToast = async () => {
    if (wasClosed.value) {
      return
    }

    try {
      const response = await getRecentPayout()
      if (response.data?.payout) {
        payoutData.value = response.data.payout
        isVisible.value = true

        timeoutId.value = setTimeout(() => {
          hideToast()
        }, 10000)
      }
    } catch (error) {
      console.warn('Failed to load recent payout:', error)
    }
  }

  const hideToast = () => {
    isVisible.value = false
    payoutData.value = null
    if (timeoutId.value) {
      clearTimeout(timeoutId.value)
      timeoutId.value = null
    }
  }

  const closeToast = () => {
    wasClosed.value = true
    hideToast()
  }

  const startToastCycle = () => {
    setTimeout(() => {
      showToast()
    }, 4000)

    intervalId.value = setInterval(() => {
      showToast()
    }, 20000)
  }

  const stopToastCycle = () => {
    if (intervalId.value) {
      clearInterval(intervalId.value)
      intervalId.value = null
    }
    if (timeoutId.value) {
      clearTimeout(timeoutId.value)
      timeoutId.value = null
    }
    hideToast()
  }

  onMounted(() => {
    startToastCycle()
  })

  onUnmounted(() => {
    stopToastCycle()
  })

  return {
    isVisible,
    payoutData,
    hideToast,
    closeToast
  }
}
