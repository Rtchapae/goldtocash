<template>
	<div data-element="value-calculator">
		<div class="container" style="max-width: unset;">
			<div class="row">
				<div class="col-md-4 p-0" data-element="calculator-image"></div>
				<div class="col-md-8 py-4 px-5">
					<h1 class="text-center">
						{{ heading }}
					</h1>
					<p class="text-center color-gold font-weight- mb-0" style="letter-spacing:2px;">
						CURRENT GOLD MARKET PRICE <span data-element="current-price">
							<span v-if="isLoading">Loading...</span>
							<span v-else-if="error">Error loading price</span>
							<span v-else-if="price">{{ price }}</span>
							<span v-else>—</span>
						</span>/OZT
					</p>
					<div class="row">
						<div class="col-12 col-md-4">
							<label for="unit-weight" class="mt-3">Weight</label>
							<input 
								type="text" 
								data-element="unit-weight" 
								name="unit-weight" 
								placeholder="Weight"
								class="form-control"
								v-model="weight"
								@input="handleWeightInput"
							>
						</div>
						<div class="col-12 col-md-4">
							<label for="unit" class="mt-3">Units</label>
							<select 
								name="unit" 
								data-element="unit-type-select" 
								class="form-control"
								v-model="selectedUnit"
							>
								<option value="" disabled="">Select Unit</option>
								<option v-for="unit in GOLD_UNITS" :key="unit.value" :value="unit.value">{{ unit.label }}</option>
							</select>
						</div>
						<div class="col-12 col-md-4">
							<label for="purity" class="mt-3">Purity</label>
							<select 
								data-element="purity-select" 
								name="purity" 
								class="form-control"
								v-model="selectedPurity"
							>
								<option value="" disabled="">Select Purity</option>
								<option v-for="purity in GOLD_PURITY" :key="purity.value" :value="purity.value">{{ purity.label }}</option>
							</select>
						</div>
						<div class="col-lg-6">
							<h3 data-element="value-display" class="color-gold text-right">{{ calculatedValue }}</h3>
						</div>
						<div class="col-lg-6">
							<button class="btn mt-3 btn-lg btn-kit" style="background:#C39E3D;" @click="handleSellClick">
								Sell My Gold Now
							</button>
						</div>
					</div>
					<div>
						<p class="text-center text-muted mt-3" style="font-size:.85em">
							This calculation provides the highest melt value of the gold today and is not an
							estimate of what we will pay. Your offer will depend on your appraisal. Use this for
							informational purposes only.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch, inject } from 'vue'
import { GOLD_UNITS, GOLD_PURITY } from '@/constants/goldCalculator'
import { getCurrentGoldPrice } from '@/api/calculator'

const props = defineProps({
	heading: {
		type: String,
		default: 'What is your gold worth?'
	},
	currentPrice: {
		type: String,
		default: null
	},
	useLegacyHandler: {
		type: Boolean,
		default: false
	},
	/** When true, "Sell My Gold Now" scrolls to top (e.g. mobile home). When false, opens kit modal (e.g. gold-calculator page). */
	scrollToTopOnSell: {
		type: Boolean,
		default: false
	}
})

const openKitModal = inject('openKitModal', null)
const price = ref(props.currentPrice || null)
const pricePerTOz = ref(null)
const weight = ref('')
const selectedUnit = ref('')
const selectedPurity = ref('')
const isLoading = ref(false)
const error = ref(null)

const formatCurrency = (value) => {
	if (!value || isNaN(value) || value <= 0) {
		return '$0.00'
	}
	return new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(value)
}

const selectedUnitObj = computed(() => {
	return GOLD_UNITS.find(u => u.value === selectedUnit.value) || null
})

const calculatedValue = computed(() => {
	if (!pricePerTOz.value || !weight.value || !selectedUnit.value || !selectedPurity.value) {
		return '$0.00'
	}

	const unit = selectedUnitObj.value
	if (!unit) {
		return '$0.00'
	}

	const weightNum = parseFloat(weight.value)
	const purityNum = parseFloat(selectedPurity.value)

	if (isNaN(weightNum) || weightNum <= 0 || isNaN(purityNum)) {
		return '$0.00'
	}

	const value = (pricePerTOz.value / unit.timesInTroyOz) * weightNum * (purityNum / 100)
	
	return formatCurrency(value)
})

const handleWeightInput = (event) => {
	const value = event.target.value
	const cleaned = value.replace(/[^0-9.]/g, '')
	const parts = cleaned.split('.')
	if (parts.length > 2) {
		weight.value = parts[0] + '.' + parts.slice(1).join('')
	} else {
		weight.value = cleaned
	}
}

const extractNumericPrice = (priceStr) => {
	if (!priceStr) return null
	const cleaned = priceStr.replace(/[$,]/g, '')
	const num = parseFloat(cleaned)
	return isNaN(num) ? null : num
}

watch(price, (newPrice) => {
	pricePerTOz.value = extractNumericPrice(newPrice)
}, { immediate: true })

onMounted(async () => {
	if (!props.currentPrice) {
		isLoading.value = true
		error.value = null
		try {
			const priceData = await getCurrentGoldPrice()
			price.value = priceData.formatted
			pricePerTOz.value = priceData.numeric
		} catch (err) {
			console.error('Failed to load gold price:', err)
			error.value = err.message || 'Failed to load price'
			price.value = null
			pricePerTOz.value = null
		} finally {
			isLoading.value = false
		}
	} else {
		pricePerTOz.value = extractNumericPrice(props.currentPrice)
	}
	
	if (GOLD_PURITY.length > 0) {
		const defaultPurity = GOLD_PURITY.find(p => p.value === '99.9')
		if (defaultPurity) {
			selectedPurity.value = defaultPurity.value
		}
	}
})

const handleSellClick = () => {
	if (props.useLegacyHandler) {
		if (typeof window !== 'undefined' && window.showGetStartedModal) {
			window.showGetStartedModal()
		} else if (window.self !== window.top && window.parent && window.parent.showGetStartedModal) {
			window.parent.showGetStartedModal()
		}
	} else if (props.scrollToTopOnSell) {
		window.scrollTo({ top: 0, behavior: 'smooth' })
	} else {
		if (typeof openKitModal === 'function') {
			openKitModal()
		} else if (typeof window !== 'undefined') {
			window.dispatchEvent(new CustomEvent('open-kit-modal'))
		}
	}
}
</script>

<style scoped>
</style>

