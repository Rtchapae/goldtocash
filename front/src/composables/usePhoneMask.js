import { onBeforeUnmount, ref } from 'vue'
import IMask from 'imask'

export function usePhoneMask(initialValue = '') {
	const phoneValue = ref(initialValue)
	const phoneInputRef = ref(null)
	let maskInstance = null

	const setupMask = (element) => {
		if (!element || maskInstance) return

		if (maskInstance) {
			maskInstance.destroy()
			maskInstance = null
		}

		// Accept +1 (000) 000-0000 so leading 1 doesn't eat the last digit
		maskInstance = IMask(element, {
			mask: '+1 (000) 000-0000',
			lazy: true,
			placeholderChar: '',
			overwrite: true,
		})

		if (phoneValue.value) {
			maskInstance.value = phoneValue.value
		}

		maskInstance.on('accept', () => {
			phoneValue.value = maskInstance.value
		})

		maskInstance.on('complete', () => {
			phoneValue.value = maskInstance.value
		})
	}

	onBeforeUnmount(() => {
		if (maskInstance) {
			maskInstance.destroy()
			maskInstance = null
		}
	})

	return {
		phoneValue,
		phoneInputRef,
		setupMask,
	}
}



