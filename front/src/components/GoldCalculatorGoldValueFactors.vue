<template>
	<section
		id="gold-calculator-figma-38-2626"
		class="gc-gvf"
		aria-labelledby="gc-gvf-heading"
		data-figma-node="38-2626"
	>
		<div class="gc-gvf__inner">
			<header class="gc-gvf__header">
				<h2 id="gc-gvf-heading" class="gc-gvf__title">
					Understanding The Factors<br />
					That Affect Gold Value
				</h2>
				<p class="gc-gvf__intro">
					When you use our calculator, remember that your gold’s value depends on a few key factors:
				</p>
			</header>

			<div class="gc-gvf__table-wrap">
				<table class="gc-gvf__table">
					<thead>
						<tr>
							<th class="gc-gvf__th" scope="col">FACTOR</th>
							<th class="gc-gvf__th" scope="col">WHY IT MATTERS</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(row, i) in rows" :key="i">
							<td class="gc-gvf__factor">
								<span class="gc-gvf__factor-text">{{ row.factor }}</span>
							</td>
							<td class="gc-gvf__why">{{ row.why }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<p class="gc-gvf__footnote">
				Understanding these factors helps you make confident, informed decisions when selling your gold.
			</p>
		</div>
	</section>
</template>

<script setup>
const rows = [
	{
		factor: 'Karat\n(Purity)',
		why: 'The higher the karat, the more pure gold it contains. 24K is pure gold; 10K is about 41.7% gold.'
	},
	{
		factor: 'Weight',
		why: 'Measured in grams, ounces, or pennyweights — more gold equals higher value.'
	},
	{
		factor: 'Gold Spot\nPrice',
		why: 'The current market rate per ounce or gram of gold. Prices change daily.'
	},
	{
		factor: 'Condition/\nComposition',
		why: 'Some items include other metals, enamel, or stones, which slightly affect melt value.'
	}
]
</script>

<style scoped>
.gc-gvf {
	width: 100%;
	background: #000;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-gvf__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-gvf__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1228px;
	text-align: center;
}

.gc-gvf__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.17;
	color: #fff;
}

.gc-gvf__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #cecece;
	max-width: 1228px;
}

.gc-gvf__table-wrap {
	width: 100%;
	max-width: 1020px;
	overflow-x: auto;
	-webkit-overflow-scrolling: touch;
}

.gc-gvf__table {
	width: 100%;
	min-width: 600px;
	border-collapse: separate;
	border-spacing: 20px 23px;
	table-layout: fixed;
	box-sizing: border-box;
}

.gc-gvf__table thead tr th {
	width: 292px;
}

.gc-gvf__table thead tr th:last-child {
	width: auto;
}

.gc-gvf__th {
	min-height: 150px;
	padding: 15px 17px 17px;
	box-sizing: border-box;
	background: #dbaf3e;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.125rem, 2.2vw, 1.625rem);
	line-height: 1.23;
	text-align: center;
	color: #000;
	vertical-align: middle;
}

.gc-gvf__factor,
.gc-gvf__why {
	min-height: 150px;
	padding: 15px 17px 17px;
	box-sizing: border-box;
	border: 2px solid #fff;
	vertical-align: middle;
	text-align: center;
}

.gc-gvf__factor {
	width: 292px;
}

.gc-gvf__factor-text {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.125rem, 2.2vw, 1.625rem);
	line-height: 1.23;
	color: #dbaf3e;
	white-space: pre-line;
	display: inline-block;
	text-align: center;
}

.gc-gvf__why {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 1.5rem);
	line-height: 1.33;
	color: #fff;
}

.gc-gvf__footnote {
	margin: 0;
	max-width: 1228px;
	width: 100%;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	text-align: center;
	color: #dbaf3e;
}

@media (max-width: 991.98px) {
	.gc-gvf {
		padding: 60px 0 80px;
	}

	.gc-gvf__table-wrap {
		margin-left: -12px;
		margin-right: -12px;
		padding-left: 12px;
		padding-right: 12px;
	}
}

@media (max-width: 767.98px) {
	.gc-gvf__table-wrap {
		margin-left: 0;
		margin-right: 0;
		padding-left: 0;
		padding-right: 0;
		max-width: 100%;
		overflow-x: visible;
	}

	.gc-gvf__table {
		min-width: 0;
		width: 100%;
		border-spacing: 6px 10px;
		table-layout: fixed;
	}

	.gc-gvf__table thead tr th {
		width: auto;
	}

	.gc-gvf__th {
		min-height: 0;
		padding: 8px 6px;
		font-size: clamp(0.75rem, 2.8vw, 0.9375rem);
		line-height: 1.2;
	}

	.gc-gvf__factor,
	.gc-gvf__why {
		min-height: 0;
		padding: 8px 6px;
		font-size: clamp(0.75rem, 2.8vw, 0.9375rem);
		line-height: 1.3;
	}

	.gc-gvf__factor {
		width: 34%;
	}

	.gc-gvf__factor-text {
		font-size: clamp(0.75rem, 2.8vw, 0.9375rem);
	}

	.gc-gvf__why {
		font-size: clamp(0.75rem, 2.6vw, 0.9375rem);
	}
}
</style>
