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
			reject(new Error('Failed to load Google Maps API'))
		}
		document.head.appendChild(script)
	})

	return loadPromise
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

/**
 * Initialize Google Places Autocomplete on an address input.
 * @param {Object} options
 * @param {import('vue').Ref<HTMLInputElement|null>} [options.inputRef] - Vue ref to input element
 * @param {string} [options.inputId] - ID of input element (if no ref)
 * @param {Function} [options.onPlaceSelect] - Callback(place, addressData) when user selects a place
 * @param {string[]} [options.fields] - Place fields to request (default: address_components, geometry, name, formatted_address)
 * @returns {{ init: () => Promise<void>, cleanup: () => void }}
 */
export function useGooglePlacesAddress({ inputRef, inputId, onPlaceSelect, fields = ['address_components', 'geometry', 'name', 'formatted_address'] }) {
	let autocompleteInstance = null
	let attachedInput = null

	const getInput = () => {
		if (inputRef?.value) return inputRef.value
		if (inputId && typeof document !== 'undefined') return document.getElementById(inputId)
		return null
	}

	const init = async () => {
		if (!API_KEY) {
			return
		}

		const Autocomplete = await loadGoogleMapsScript()
		if (!Autocomplete) {
			return
		}

		const input = getInput()
		if (!input || !input.isConnected) {
			return
		}

		if (autocompleteInstance && attachedInput === input) {
			return
		}

		if (autocompleteInstance) {
			try {
				google?.maps?.event?.clearInstanceListeners?.(autocompleteInstance)
			} catch (_) {}
			autocompleteInstance = null
			attachedInput = null
		}

		const options = {
			componentRestrictions: { country: 'us' },
			fields,
			types: ['address']
		}

		try {
			autocompleteInstance = new Autocomplete(input, options)
			attachedInput = input
		} catch (_) {
			return
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
	}

	const cleanup = () => {
		if (autocompleteInstance) {
			try {
				google?.maps?.event?.clearInstanceListeners?.(autocompleteInstance)
			} catch (_) {}
			autocompleteInstance = null
			attachedInput = null
		}
	}

	return { init, cleanup }
}
