import { onMounted, onBeforeUnmount, ref } from 'vue'
import IMask from 'imask'

export function usePhoneMask(initialValue = '') {
	const phoneValue = ref(initialValue)
	const phoneInputRef = ref(null)
	let maskInstance = null

	const setupMask = (element) => {
		if (!element) return

		maskInstance = IMask(element, {
			mask: '(000) 000-0000',
			lazy: false,
		})

		if (phoneValue.value) {
			maskInstance.value = phoneValue.value
		}

		maskInstance.on('accept', () => {
			phoneValue.value = maskInstance.value
		})
	}

	onMounted(() => {
		if (phoneInputRef.value) {
			setupMask(phoneInputRef.value)
		}
	})

	onBeforeUnmount(() => {
		if (maskInstance) {
			maskInstance.destroy()
		}
	})

	return {
		phoneValue,
		phoneInputRef,
		setupMask,
	}
}




