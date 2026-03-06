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

		maskInstance = IMask(element, {
			mask: '(000) 000-0000',
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



