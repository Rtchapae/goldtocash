<template>
	<div
		class="vc-figma"
		:class="{ 'vc-figma--tabbed': isTabbed }"
		data-element="value-calculator"
		:data-ui="isTabbed ? 'figma-62-786-tabbed' : 'figma-38-2126'"
	>
		<div class="vc-figma__row" :class="{ 'vc-figma__row--tabbed': isTabbed }">
			<aside
				v-if="!isTabbed"
				class="vc-figma__market"
				data-element="calculator-image"
				aria-label="Current gold market price"
			>
				<img
					class="vc-figma__market-img"
					src="/images/AdobeStock_1519762262 1.png"
					alt=""
					width="350"
					height="365"
					loading="eager"
					decoding="async"
				/>
				<div class="vc-figma__market-strip">
					<p class="vc-figma__market-label">CURRENT GOLD MARKET PRICE</p>
					<p class="vc-figma__market-price" data-element="current-price">
						<span v-if="isLoading">Loading…</span>
						<span v-else-if="error">Error loading price</span>
						<span v-else-if="price">{{ price }}</span>
						<span v-else>—</span>
						<span class="vc-figma__market-ozt">/OZT</span>
					</p>
				</div>
			</aside>

			<div class="vc-figma__calc" :class="{ 'vc-figma__calc--tabbed': isTabbed }">
				<h2 class="vc-figma__title" :class="{ 'vc-figma__title--tabbed': isTabbed }" v-html="headingHtml"></h2>

				<div v-if="isTabbed" class="vc-figma__tabbed-market" aria-live="polite">
					<p class="vc-figma__tabbed-market-label">CURRENT GOLD MARKET PRICE</p>
					<p class="vc-figma__tabbed-market-price">
						<span v-if="isLoading">Loading…</span>
						<span v-else-if="error">Error loading price</span>
						<span v-else-if="price">{{ price }}</span>
						<span v-else>—</span>
						<span class="vc-figma__tabbed-market-ozt"> /OZT</span>
					</p>
				</div>

				<div class="vc-figma__fields" :class="{ 'vc-figma__fields--tabbed': isTabbed }">
					<div class="vc-figma__field">
						<label class="vc-figma__label" :for="fieldId('unit-weight')">Weight</label>
						<input
							:id="fieldId('unit-weight')"
							type="text"
							inputmode="decimal"
							autocomplete="off"
							data-element="unit-weight"
							name="unit-weight"
							class="vc-figma__input"
							placeholder="Enter weight"
							v-model="weight"
							@input="handleWeightInput"
						/>
					</div>
					<div class="vc-figma__field">
						<label class="vc-figma__label" :for="fieldId('unit-type-select')">Weight Unit:</label>
						<div class="vc-figma__select-wrap">
							<select
								:id="fieldId('unit-type-select')"
								name="unit"
								data-element="unit-type-select"
								class="vc-figma__select"
								v-model="selectedUnit"
							>
								<option value="" disabled>Select Unit</option>
								<option v-for="unit in GOLD_UNITS" :key="unit.value" :value="unit.value">
									{{ unit.label }}{{ unit.label === 'Gram' ? 's' : '' }}
								</option>
							</select>
						</div>
					</div>
					<div class="vc-figma__field">
						<label class="vc-figma__label" :for="fieldId('purity-select')">Gold Karat:</label>
						<div class="vc-figma__select-wrap">
							<select
								:id="fieldId('purity-select')"
								data-element="purity-select"
								name="purity"
								class="vc-figma__select"
								v-model="selectedPurity"
							>
								<option value="" disabled>Select Karat</option>
								<option v-for="p in GOLD_PURITY" :key="p.value" :value="p.value">
									{{ karatLabel(p) }}
								</option>
							</select>
						</div>
					</div>
				</div>

				<div class="vc-figma__actions" :class="{ 'vc-figma__actions--tabbed': isTabbed }">
					<div class="vc-figma__pay">
						<p class="vc-figma__pay-label">we will pay you</p>
						<p class="vc-figma__pay-value" data-element="value-display">{{ calculatedValue }}</p>
					</div>
					<button type="button" class="vc-figma__cta" @click="handleSellClick">
						SELL MY GOLD NOW
					</button>
				</div>

				<div class="vc-figma__disclaimer-wrap">
					<p class="vc-figma__disclaimer">
						This calculation provides the highest melt value of the gold today and is not an estimate of what we will pay.
						Your offer will depend on your appraisal. Use this for informational purposes only.
					</p>
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
	scrollToTopOnSell: {
		type: Boolean,
		default: false
	},
	/** Prefix for input/select ids when multiple calculators exist on one page (e.g. "tabbed-calc"). */
	fieldIdPrefix: {
		type: String,
		default: ''
	},
	/** `tabbed`: single-column card, market strip inside the calculator (no side image). */
	variant: {
		type: String,
		default: 'default',
		validator: (v) => ['default', 'tabbed'].includes(v),
	},
})

const isTabbed = computed(() => props.variant === 'tabbed')

function fieldId(suffix) {
	return props.fieldIdPrefix ? `${props.fieldIdPrefix}-${suffix}` : suffix
}

const openKitModal = inject('openKitModal', null)
const price = ref(props.currentPrice || null)
const pricePerTOz = ref(null)
const weight = ref('')
const selectedUnit = ref('')
const selectedPurity = ref('')
const isLoading = ref(false)
const error = ref(null)

function karatLabel(p) {
	return `${p.label.replace(/k$/i, '').trim()} Karat`
}

function escapeHtml(s) {
	return String(s)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
}

const headingHtml = computed(() => {
	const h = escapeHtml(props.heading || '')
	// Tabbed headings: plain title (no gold accent); default layout keeps gold highlight
	if (props.variant === 'tabbed') {
		return h
	}
	return h.replace(/\b(gold)\b/gi, '<span class="vc-figma__title-accent">$1</span>')
})

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
.vc-figma {
	width: 100%;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
	/* Global styles set a cream backdrop; keep root transparent so the white card corners read cleanly. */
	background: transparent !important;
}

.vc-figma__row {
	display: flex;
	flex-direction: row;
	align-items: stretch;
	justify-content: center;
	gap: 6px;
	flex-wrap: wrap;
	box-sizing: border-box;
	min-width: 0;
	max-width: 100%;
}

/* Left: full-bleed photo; height matches calculator card (row stretch). */
.vc-figma__market {
	position: relative;
	flex: 0 0 350px;
	width: 350px;
	max-width: 100%;
	min-height: 365px;
	border-radius: 12px;
	background: #faeed4;
	background-image: none !important;
	background-size: auto !important;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
	box-sizing: border-box;
	overflow: hidden;
}

.vc-figma__market-img {
	position: absolute;
	inset: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: center;
	display: block;
}

.vc-figma__market-strip {
	position: absolute;
	left: 12px;
	right: 12px;
	bottom: 36px;
	z-index: 1;
	background: transparent;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 6px;
	box-sizing: border-box;
}

.vc-figma__market-label {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 0.95rem;
	line-height: 1.26;
	color: #dbaf3e;
	text-shadow:
		0 0 1px rgba(0, 0, 0, 0.8),
		0 1px 4px rgba(0, 0, 0, 0.65),
		0 0 20px rgba(0, 0, 0, 0.45);
}

.vc-figma__market-price {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.5rem, 4vw, 1.9rem);
	line-height: 1.1;
	color: #c39e3d;
	text-shadow:
		0 0 1px rgba(0, 0, 0, 0.85),
		0 2px 6px rgba(0, 0, 0, 0.7),
		0 0 24px rgba(0, 0, 0, 0.5);
}

.vc-figma__market-ozt {
	font-size: 0.85em;
	font-weight: 600;
}

/* Right: calculator card */
.vc-figma__calc {
	flex: 1 1 520px;
	min-width: min(100%, 320px);
	max-width: 872px;
	min-height: 365px;
	align-self: stretch;
	background-color: #fff;
	border-radius: 12px;
	box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
	padding: 20px clamp(16px, 3vw, 40px) 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

/* Tabbed variant: single column, market inside card, no left image column */
.vc-figma--tabbed {
	width: 100%;
	background: #fff;
}

.vc-figma__row--tabbed {
	flex-direction: column;
	align-items: stretch;
	background: #fff;
}

.vc-figma__calc--tabbed {
	flex: 1 1 auto;
	width: 100%;
	max-width: 520px;
	margin-left: auto;
	margin-right: auto;
	min-height: auto;
	padding: clamp(28px, 4vw, 44px) clamp(20px, 4vw, 40px) clamp(24px, 3vw, 36px);
	gap: clamp(20px, 3vw, 32px);
	box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
}

.vc-figma__title--tabbed {
	text-align: center;
	font-size: clamp(1.5rem, 4vw, 2rem);
	line-height: 1.125;
}

.vc-figma__tabbed-market {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	align-self: stretch;
	width: 100%;
	gap: 0;
	text-align: left;
}

.vc-figma__tabbed-market-label {
	margin: 0 0 12px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 1.25rem;
	line-height: 1.3;
	color: #c39e3d;
}

.vc-figma__tabbed-market-price {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.75rem, 4vw, 2.375rem);
	line-height: 1.25;
	color: #000;
}

.vc-figma__tabbed-market-ozt {
	font-size: 0.85em;
	font-weight: 600;
}

.vc-figma__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.25rem, 3vw, 2rem);
	line-height: 1;
	text-align: left;
	color: #000;
}

.vc-figma__title :deep(.vc-figma__title-accent) {
	color: inherit;
	font: inherit;
	line-height: inherit;
}

.vc-figma__fields {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: clamp(16px, 2vw, 24px);
	align-items: start;
	width: 100%;
	box-sizing: border-box;
}

/* Must follow .vc-figma__fields — base rule was overriding .vc-figma__fields--tabbed */
.vc-figma__calc--tabbed .vc-figma__fields {
	grid-template-columns: 1fr;
	gap: 20px;
}

.vc-figma__field {
	display: flex;
	flex-direction: column;
	gap: 10px;
	min-width: 0;
}

.vc-figma__label {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 1.125rem;
	line-height: 1.11;
	color: rgba(0, 0, 0, 0.3);
}

.vc-figma__input {
	width: 100%;
	box-sizing: border-box;
	padding: 12px 16px;
	border-radius: 12px;
	border: 1px solid #c39e3d !important;
	background: #e6e6e6 !important;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 1.125rem;
	line-height: 1.11;
	text-align: center;
	color: #c39e3d;
	box-shadow: none !important;
}

.vc-figma__input:focus {
	outline: none;
	background: #e6e6e6 !important;
	border-color: #c39e3d !important;
	box-shadow: 0 0 0 2px rgba(195, 158, 61, 0.35) !important;
}

.vc-figma__input::placeholder {
	color: #c39e3d;
	opacity: 0.85;
}

.vc-figma__select-wrap {
	width: 100%;
}

.vc-figma__select {
	width: 100%;
	box-sizing: border-box;
	appearance: none;
	padding: 12px 40px 12px 16px;
	border: none !important;
	border-radius: 12px !important;
	background-color: #fff9ee !important;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23000' d='M1.41 0 6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position: right 14px center;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 1.125rem;
	line-height: 1.11;
	text-align: center;
	color: #000;
	cursor: pointer;
	box-shadow: none !important;
}

.vc-figma__select:focus {
	outline: none;
	background-color: #fff9ee !important;
	box-shadow: 0 0 0 2px rgba(195, 158, 61, 0.35) !important;
}

.vc-figma__actions {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	gap: clamp(8px, 3vw, 32px);
	flex-wrap: nowrap;
	margin-top: 15px;
	width: 100%;
	min-width: 0;
	box-sizing: border-box;
}

.vc-figma__pay {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	justify-content: center;
	gap: 10px;
	flex: 0 1 auto;
	min-width: 0;
}

.vc-figma__pay-label {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 0.875rem;
	line-height: 1.43;
	text-transform: uppercase;
	color: #000;
}

.vc-figma__pay-value {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(2rem, 5vw, 3.125rem);
	line-height: 1;
	text-align: center;
	color: #000;
	position: static !important;
	margin-top: 0 !important;
	padding: 0 !important;
	min-width: 0;
	max-width: 100%;
}

/* Disable legacy dotted frame on value-display from _main_page.scss */
.vc-figma [data-element="value-display"]::after {
	content: none !important;
	display: none !important;
}

.vc-figma__cta {
	flex: 1 1 0;
	min-width: 0;
	max-width: 100%;
	min-height: clamp(56px, 10vw, 80px);
	padding: clamp(12px, 2.2vw, 24px) clamp(12px, 2.8vw, 32px);
	border: none;
	border-radius: 999px;
	background: #c39e3d;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(0.6875rem, calc(0.55rem + 1.35vw), 1.125rem);
	line-height: 1.1;
	text-align: center;
	color: #fff;
	cursor: pointer;
	box-sizing: border-box;
	white-space: nowrap;
	transition: background 0.15s ease, transform 0.05s ease;
}

.vc-figma__cta:hover {
	background: #b08e35;
}

.vc-figma__cta:active {
	transform: scale(0.99);
}

.vc-figma__actions--tabbed {
	flex-direction: column;
	align-items: stretch;
	justify-content: flex-start;
	gap: 20px;
	margin-top: 8px;
}

.vc-figma__actions--tabbed .vc-figma__pay {
	align-items: flex-start;
	width: 100%;
}

.vc-figma__actions--tabbed .vc-figma__pay-label {
	color: #c39e3d;
}

.vc-figma__actions--tabbed .vc-figma__pay-value {
	text-align: left;
}

.vc-figma__actions--tabbed .vc-figma__cta {
	width: 100%;
	min-width: 0;
	max-width: 100%;
	align-self: center;
}

.vc-figma__disclaimer-wrap {
	align-self: stretch;
	min-width: 0;
	max-width: 100%;
}

.vc-figma__disclaimer {
	margin: 0;
	margin-top: 8px;
	flex-shrink: 1;
	min-width: 0;
	max-width: 100%;
	width: 100%;
	box-sizing: border-box;
	overflow-wrap: anywhere;
	word-break: break-word;
	hyphens: auto;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(0.75rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	text-align: center;
	color: rgba(0, 0, 0, 0.3);
}

@media (max-width: 991.98px) {
	.vc-figma__row {
		flex-direction: column;
		align-items: stretch;
	}

	.vc-figma__market {
		width: 100%;
		flex: 1 1 auto;
		min-height: 240px;
		aspect-ratio: auto;
		height: auto;
		max-height: none;
	}

	.vc-figma__calc {
		max-width: 100%;
		min-width: 0;
		min-height: auto;
		height: auto;
	}

	.vc-figma__fields {
		grid-template-columns: 1fr;
	}

	.vc-figma__actions {
		flex-direction: column;
		align-items: stretch;
		justify-content: flex-start;
		gap: 20px;
	}

	.vc-figma__pay {
		align-items: center;
		width: 100%;
	}

	.vc-figma__pay-value {
		text-align: center;
	}

	.vc-figma__cta {
		width: 100%;
		min-width: 0;
	}

	.vc-figma__disclaimer {
		font-size: 1.25rem;
		line-height: 1.35;
		padding-left: 0;
		padding-right: 0;
	}

	.vc-figma__title {
		font-size: 1.875rem;
		line-height: 1.2;
	}

	.vc-figma__title--tabbed {
		font-size: 1.875rem;
		line-height: 1.2;
	}
}

@media (max-width: 767.98px) {
	.vc-figma__market {
		aspect-ratio: 1;
		min-height: 0;
		width: 100%;
		max-width: 100%;
	}

	.vc-figma__market-strip {
		left: 8px;
		right: 8px;
		bottom: 24px;
		gap: 12px;
	}

	.vc-figma__market-label {
		font-size: clamp(1.125rem, 3.8vw, 1.375rem);
		line-height: 1.2;
	}

	.vc-figma__market-price {
		font-size: clamp(1.625rem, 6.8vw, 2.25rem);
		line-height: 1.12;
	}

	.vc-figma__market-ozt {
		font-size: 0.88em;
	}
}

/* Desktop: filter drop-shadow follows rounded shape without box-shadow corner artifacts */
@media (min-width: 992px) {
	.vc-figma__market {
		box-shadow: none;
		filter: drop-shadow(0 4px 18px rgba(0, 0, 0, 0.1));
	}

	.vc-figma__calc {
		box-shadow: none;
		filter: drop-shadow(0 4px 22px rgba(0, 0, 0, 0.12));
	}

	.vc-figma__calc--tabbed {
		box-shadow: none;
		filter: drop-shadow(0 4px 22px rgba(0, 0, 0, 0.1));
	}
}
</style>
