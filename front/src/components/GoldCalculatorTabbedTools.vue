<template>
	<section
		id="gold-calculator-figma-62-786"
		class="gc-tab-calc"
		aria-labelledby="gc-tab-calc-heading"
		data-figma-node="62-786"
	>
		<div class="gc-tab-calc__inner">
			<header class="gc-tab-calc__header">
				<h2 id="gc-tab-calc-heading" class="gc-tab-calc__title">Additional Gold Calculator Tools</h2>
				<p class="gc-tab-calc__intro">
					To help you get the most accurate estimate, we offer a set of specialized calculators:
				</p>
			</header>

			<div class="gc-tab-calc__shell">
				<div class="gc-tab-calc__tabs" role="tablist" aria-label="Calculator type">
					<button
						v-for="(tab, index) in tabs"
						:key="tab.id"
						type="button"
						class="gc-tab-calc__tab"
						:class="{ 'gc-tab-calc__tab--active': activeTab === index }"
						role="tab"
						:aria-selected="activeTab === index"
						:id="`gc-tab-calc-tab-${tab.id}`"
						:tabindex="activeTab === index ? 0 : -1"
						@click="activeTab = index"
					>
						<span class="gc-tab-calc__tab-text">
							<span class="gc-tab-calc__tab-line">{{ tab.label[0] }}</span>
							<span class="gc-tab-calc__tab-line">{{ tab.label[1] }}</span>
						</span>
					</button>
				</div>

				<div
					class="gc-tab-calc__panel"
					role="tabpanel"
					:aria-labelledby="`gc-tab-calc-tab-${tabs[activeTab].id}`"
				>
					<p class="gc-tab-calc__lead">{{ tabs[activeTab].intro }}</p>
					<div class="gc-tab-calc__calc">
						<ClientOnly>
							<ValueCalculator
								variant="tabbed"
								:heading="tabs[activeTab].heading"
								field-id-prefix="tabbed-calc"
							/>
						</ClientOnly>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref } from 'vue'
import ClientOnly from '@/components/ClientOnly.vue'
import ValueCalculator from '@/components/ValueCalculator.vue'

const activeTab = ref(0)

const tabs = [
	{
		id: 'dental',
		label: ['DENTAL GOLD', 'VALUE CALCULATOR'],
		heading: 'Dental Gold Value Calculator',
		intro:
			'Estimate the worth of dental crowns, bridges, and other gold dental alloys. Even if it looks small, dental gold can have significant value.',
	},
	{
		id: 'scrap',
		label: ['SCRAP GOLD', 'CALCULATOR'],
		heading: 'Scrap Gold Value Calculator',
		intro:
			'Turn your broken, outdated, or unused jewelry into cash. Find out how much your scrap gold is worth instantly.',
	},
	{
		id: 'melt',
		label: ['GOLD MELT', 'VALUE CALCULATOR'],
		heading: 'Gold Melt Value Calculator',
		intro:
			'Get precise value estimates for melted or refined gold. Perfect for jewelers, refiners, and serious collectors who want to calculate pure gold melt prices.',
	},
]
</script>

<style scoped>
/* Tabbed calculators strip: tinted band, tab row, shared calculator panel. */
.gc-tab-calc {
	width: 100%;
	background: #c39e3d4d;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-tab-calc__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-tab-calc__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	text-align: center;
}

.gc-tab-calc__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #131615;
}

@media (min-width: 992px) {
	.gc-tab-calc__title {
		font-size: 48px;
		line-height: 56px;
	}
}

.gc-tab-calc__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #000;
	max-width: 1226px;
}

.gc-tab-calc__shell {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	box-sizing: border-box;
	background: transparent;
}

.gc-tab-calc__tabs {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: flex-end;
	gap: 10px;
	padding: 0 30px;
	box-sizing: border-box;
	background: transparent;
}

.gc-tab-calc__tab {
	flex: 1 1 200px;
	max-width: 320px;
	min-height: 109px;
	margin: 0;
	padding: 15px 24px;
	box-sizing: border-box;
	border: 2px solid #000;
	border-bottom: none;
	border-radius: 20px 20px 0 0;
	cursor: pointer;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 20px;
	line-height: 1.4;
	text-align: center;
	background: #fff;
	color: #c39e3d;
	font-weight: 500;
	transition: background 0.15s ease, color 0.15s ease;
}

.gc-tab-calc__tab--active {
	background: #000;
	color: #fff;
	font-weight: 700;
}

.gc-tab-calc__tab-text {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 2px;
}

.gc-tab-calc__tab-line {
	display: block;
}

.gc-tab-calc__panel {
	background: #fff;
	border: 2px solid #000;
	border-radius: 20px;
	box-shadow: 15px 15px 0 0 #000;
	padding: 24px 20px 28px;
	box-sizing: border-box;
}

.gc-tab-calc__lead {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 24px);
	line-height: 1.333;
	color: #000;
}

.gc-tab-calc__calc {
	width: 100%;
	background: #fff;
}

.gc-tab-calc__calc :deep(.vc-figma) {
	margin: 0;
}

@media (max-width: 991.98px) {
	.gc-tab-calc {
		padding: 60px 0 80px;
	}

	.gc-tab-calc__tabs {
		padding: 0;
	}

	.gc-tab-calc__tab {
		max-width: none;
	}

	.gc-tab-calc__panel {
		box-shadow: 8px 8px 0 0 #000;
	}
}

@media (max-width: 767.98px) {
	.gc-tab-calc__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-tab-calc__intro {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-tab-calc__lead {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-tab-calc__tabs {
		flex-direction: row;
		flex-wrap: nowrap;
		justify-content: center;
		align-items: stretch;
		gap: 6px;
		padding: 0 8px;
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
		scrollbar-width: thin;
	}

	.gc-tab-calc__tab {
		flex: 1 1 0;
		min-width: 0;
		width: auto;
		max-width: none;
		min-height: 56px;
		padding: 8px 4px;
		font-size: clamp(8px, 2.4vw, 11px);
		line-height: 1.2;
	}

	.gc-tab-calc__tab-text {
		gap: 1px;
	}
}
</style>
