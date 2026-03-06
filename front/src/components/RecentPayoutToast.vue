<template>
  <Teleport v-if="isMounted" to="body">
    <Transition name="fade">
      <div
        v-if="isVisible && payoutData"
        id="recent-payout"
        class="recent-payout-toast"
        @click="closeOnMobile"
      >
        <i class="fas fa-check-circle" id="recent-payout-icon" aria-hidden="true"></i>
        <span>
          <span>{{ payoutData.firstName }}</span> got paid<br />
          <span style="font-weight: bold;">{{ formattedAmount }}</span> recently!
        </span>
        <span id="close-recent-payout" @click.stop="close">
          <i class="fa fa-times"></i>
        </span>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const isMounted = ref(false)
import { getRecentPayout } from '@/api/recentPayout'

const isVisible = ref(false)
const payoutData = ref(null)
const wasClosed = ref(false)
const intervalId = ref(null)

const formattedAmount = computed(() => {
  if (!payoutData.value?.amount) return '$0.00'

  const [dollar, cents] = payoutData.value.amount.toLocaleString().split('.')

  return `$${dollar || '0'}.${(cents || '00').padEnd(2, '0')}`
})

const fetchPayoutData = async () => {
  if (wasClosed.value) return

  try {
    const response = await getRecentPayout()
    if (response?.data?.payout) {
      payoutData.value = response.data.payout
      showToast()
    }
  } catch (error) {
    console.warn('Failed to fetch recent payout:', error)
  }
}

const showToast = () => {
  isVisible.value = true

  setTimeout(() => {
    if (isVisible.value) {
      isVisible.value = false
    }
  }, 8000)
}

const close = () => {
  isVisible.value = false
  wasClosed.value = true
}

const closeOnMobile = () => {
  if (window.innerWidth < 768) {
    close()
  }
}

onMounted(() => {
  isMounted.value = true
  intervalId.value = setInterval(() => {
    fetchPayoutData()
  }, 20000)

  setTimeout(() => {
    fetchPayoutData()
  }, 4000)
})

onUnmounted(() => {
  if (intervalId.value) {
    clearInterval(intervalId.value)
  }
})
</script>

<style scoped>
.recent-payout-toast {
  background: #219653;
  position: fixed;
  left: 25px;
  bottom: 25px;
  color: white;
  padding: 8px 14px;
  border-radius: 12px 12px 12px 0;
  font-size: 15px;
  z-index: 9999;
  font-weight: 300;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: slideUp 0.3s ease-out;
}

@media (max-width: 767px) {
  .recent-payout-toast {
    left: 10px;
    bottom: 10px;
    width: calc(100vw - 20px);
  }

  .recent-payout-toast br {
    display: none;
  }
}

#recent-payout-icon {
  font-size: 1.4rem;
  margin-right: 12px;
  flex-shrink: 0;
  opacity: 0.95;
}

#close-recent-payout {
  display: none;
  position: absolute;
  top: -8px;
  right: -8px;
  background: black;
  text-align: center;
  width: 20px;
  height: 20px;
  font-size: 0.7em;
  border-radius: 100%;
  border: solid 1px white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.recent-payout-toast:hover #close-recent-payout {
  display: flex;
}

#close-recent-payout:hover {
  opacity: 0.7;
}

@keyframes slideUp {
  from {
    transform: translateY(100px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>