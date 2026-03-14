const API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || import.meta.env.VITE_GOOGLE_API_KEY
const DEBUG = true
const log = (...args) => DEBUG && console.log('[Places]', ...args)
const warn = (...args) => console.warn('[Places]', ...args)
const err = (...args) => console.error('[Places]', ...args)

let loadPromise = null

function loadGoogleMapsScript() {
	if (typeof window === 'undefined') return Promise.resolve(null)
	if (!API_KEY) {
		warn('API key missing. Set VITE_GOOGLE_MAPS_API_KEY or VITE_GOOGLE_API_KEY in .env')
		return Promise.resolve(null)
	}
	if (window.google?.maps?.places?.Autocomplete) {
		log('Google Places API already loaded')
		return Promise.resolve(window.google.maps.places.Autocomplete)
	}
	if (loadPromise) return loadPromise

	const scriptUrl = `https://maps.googleapis.com/maps/api/js?key=${API_KEY}&libraries=places`
	log('Loading script:', scriptUrl.replace(API_KEY, 'KEY...'))
	loadPromise = new Promise((resolve, reject) => {
		const existing = document.querySelector('script[src*="maps.googleapis.com"]')
		if (existing) {
			log('Script tag already exists, waiting for API')
			const poll = () => {
				if (window.google?.maps?.places?.Autocomplete) {
					log('API ready (existing script)')
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
			log('Script onload')
			const poll = () => {
				if (window.google?.maps?.places?.Autocomplete) {
					log('API ready')
					resolve(window.google.maps.places.Autocomplete)
				} else {
					setTimeout(poll, 50)
				}
			}
			poll()
		}
		script.onerror = () => {
			err('Failed to load Google Maps API script')
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

	// Если subpremise не пришёл в components — пробуем вытащить из formatted_address (например "123 Main St, Apt 4, City, NY")
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
			warn('init skipped: no API key')
			return
		}

		log('init: loading script...')
		const Autocomplete = await loadGoogleMapsScript()
		if (!Autocomplete) {
			warn('init skipped: Autocomplete not available')
			return
		}

		const input = getInput()
		if (!input) {
			warn('init skipped: input not found (id=', inputId, ', ref=', !!inputRef?.value, ')')
			return
		}
		if (!input.isConnected) {
			warn('init skipped: input not in DOM')
			return
		}

		if (autocompleteInstance && attachedInput === input) {
			log('init skipped: already attached to this input')
			return
		}

		if (autocompleteInstance) {
			log('cleaning up previous instance')
			try {
				google?.maps?.event?.clearInstanceListeners?.(autocompleteInstance)
			} catch (e) {
				warn('cleanup error', e)
			}
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
			log('Autocomplete attached to input #' + (input.id || 'no-id'))
		} catch (e) {
			err('Autocomplete constructor error', e)
			return
		}

		if (onPlaceSelect && typeof onPlaceSelect === 'function') {
			autocompleteInstance.addListener('place_changed', () => {
				const place = autocompleteInstance.getPlace()
				log('place_changed', place)
				if (!place || (!place.formatted_address && !place.address_components?.length)) {
					log('place_changed ignored: empty place')
					return
				}
				const addressData = getAddressComponents(place)
				log('applying address', addressData)
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
