import { onMounted, onUnmounted, watch, nextTick } from 'vue'

const API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || import.meta.env.VITE_GOOGLE_API_KEY

let loadPromise = null

function loadGoogleMapsScript() {
	if (typeof window === 'undefined') return Promise.resolve(null)
	if (!API_KEY) {
		return Promise.resolve(null)
	}
	if (window.google?.maps?.places?.Autocomplete) {
		return Promise.resolve(window.google.maps.places.Autocomplete)
	}
	if (loadPromise) return loadPromise

	const scriptUrl = `https://maps.googleapis.com/maps/api/js?key=${API_KEY}&libraries=places`
	loadPromise = new Promise((resolve, reject) => {
		const existing = document.querySelector('script[src*="maps.googleapis.com"]')
		if (existing) {
			const poll = () => {
				if (window.google?.maps?.places?.Autocomplete) {
					resolve(window.google.maps.places.Autocomplete)
				} else {
					setTimeout(poll, 100)
				}
			}
			poll()
			return
		}

		const script = document.createElement('script')
		script.src = scriptUrl
		script.async = true
		script.defer = true
		script.onload = () => {
			const poll = () => {
				if (window.google?.maps?.places?.Autocomplete) {
					resolve(window.google.maps.places.Autocomplete)
				} else {
					setTimeout(poll, 50)
				}
			}
			poll()
		}
		script.onerror = () => {
			loadPromise = null
			reject(new Error('Failed to load Google Maps API'))
		}
		document.head.appendChild(script)
	})

	return loadPromise
}

function isInputVisible(input) {
	if (!input?.isConnected) return false
	const style = window.getComputedStyle(input)
	if (style.display === 'none' || style.visibility === 'hidden') return false
	const rect = input.getBoundingClientRect()
	return rect.width > 0 && rect.height > 0
}

/**
 * Extract address components from PlaceResult for US addresses.
 * @param {google.maps.places.PlaceResult} place
 * @returns {{ street: string, address2: string, city: string, state: string, zip: string, fullAddress: string }}
 */
function getAddressComponents(place) {
	const components = place?.address_components || []
	const result = {
		street: '',
		streetNumber: '',
		address2: '',
		city: '',
		state: '',
		zip: '',
		fullAddress: place?.formatted_address || place?.name || ''
	}

	for (const comp of components) {
		const types = comp.types || []
		if (types.includes('street_number')) result.streetNumber = comp.long_name
		if (types.includes('route')) result.street = comp.long_name
		if (types.includes('subpremise')) result.address2 = comp.long_name
		if (types.includes('locality')) result.city = comp.long_name
		if (types.includes('administrative_area_level_1')) result.state = comp.short_name
		if (types.includes('postal_code')) result.zip = comp.long_name
	}

	const streetLine = [result.streetNumber, result.street].filter(Boolean).join(' ').trim() || result.fullAddress
	result.street = streetLine

	if (!result.address2 && result.fullAddress) {
		const parts = result.fullAddress.split(',').map((p) => p.trim())
		const subpremiseLike = /apt\.?|suite|unit|#|floor|fl\.?|ste\.?|bldg\.?|room|rm\.?|no\.?/i
		if (parts.length >= 2) {
			const second = parts[1]
			const looksLikeApt = subpremiseLike.test(second) || (second.length <= 20 && /\d/.test(second))
			if (looksLikeApt) result.address2 = second
		}
	}

	return result
}

const INIT_RETRY_DELAYS_MS = [0, 350, 700, 1200, 2000, 3500, 5500]

/**
 * Initialize Google Places Autocomplete on an address input.
 * Retries after preloader / mobile-desktop layout swap (common on referral landings).
 */
export function useGooglePlacesAddress({
	inputRef,
	inputId,
	onPlaceSelect,
	fields = ['address_components', 'geometry', 'name', 'formatted_address'],
}) {
	let autocompleteInstance = null
	let attachedInput = null
	let destroyed = false
	let focusHandler = null
	const retryTimers = []

	const getInput = () => {
		if (inputRef?.value) return inputRef.value
		if (inputId && typeof document !== 'undefined') return document.getElementById(inputId)
		return null
	}

	const cleanup = () => {
		if (focusHandler) {
			const input = attachedInput || getInput()
			input?.removeEventListener('focus', focusHandler)
			focusHandler = null
		}
		if (autocompleteInstance) {
			try {
				google?.maps?.event?.clearInstanceListeners?.(autocompleteInstance)
			} catch (_) {}
			autocompleteInstance = null
			attachedInput = null
		}
	}

	const init = async () => {
		if (!API_KEY || destroyed) {
			return false
		}

		const Autocomplete = await loadGoogleMapsScript()
		if (!Autocomplete) {
			return false
		}

		const input = getInput()
		if (!input || !input.isConnected || !isInputVisible(input)) {
			return false
		}

		if (autocompleteInstance && attachedInput === input) {
			return true
		}

		cleanup()

		const options = {
			componentRestrictions: { country: 'us' },
			fields,
			types: ['address'],
		}

		try {
			autocompleteInstance = new Autocomplete(input, options)
			attachedInput = input
		} catch (_) {
			return false
		}

		if (onPlaceSelect && typeof onPlaceSelect === 'function') {
			autocompleteInstance.addListener('place_changed', () => {
				const place = autocompleteInstance.getPlace()
				if (!place || (!place.formatted_address && !place.address_components?.length)) {
					return
				}
				const addressData = getAddressComponents(place)
				onPlaceSelect(place, addressData)
			})
		}

		return true
	}

	const attemptInit = () => {
		init().catch((e) => {
			if (import.meta.env.DEV) {
				console.warn('[Places] init error', e)
			}
		})
	}

	const bindFocusFallback = () => {
		const input = getInput()
		if (!input || focusHandler) return
		focusHandler = () => attemptInit()
		input.addEventListener('focus', focusHandler)
	}

	const scheduleInitAttempts = () => {
		retryTimers.forEach((id) => clearTimeout(id))
		retryTimers.length = 0
		INIT_RETRY_DELAYS_MS.forEach((delay) => {
			retryTimers.push(setTimeout(attemptInit, delay))
		})
	}

	onMounted(() => {
		scheduleInitAttempts()
		bindFocusFallback()

		window.addEventListener('gtc-app-ready', attemptInit)
		window.addEventListener('gtc-layout-ready', attemptInit)

		watch(
			() => inputRef?.value,
			(el) => {
				if (el) {
					bindFocusFallback()
					attemptInit()
				}
			}
		)

		nextTick(() => bindFocusFallback())
	})

	onUnmounted(() => {
		destroyed = true
		retryTimers.forEach((id) => clearTimeout(id))
		window.removeEventListener('gtc-app-ready', attemptInit)
		window.removeEventListener('gtc-layout-ready', attemptInit)
		cleanup()
	})

	return { init, cleanup }
}
