<template>
	<div>
		<div class="">
			<p class="text-sm text-secondary-light mb-0">{{ branchName }}</p>
		</div>
		<div class="row g-3 mb-4">
			<div class="col-xxl-6 col-sm-12">
				<StatCard
					label="Transaction"
					:value="data?.transactions || 0"
					icon="fa6-solid:file-invoice-dollar"
					icon-bg-class="bg-red"
					icon-class="text-white text-2xl mb-0"
					gradient-class="bg-gradient-start-5"
					:change-percent="changePercent"
				/>
			</div>
			<div class="col-xxl-6 col-sm-12">
				<StatCard
					label="Paid amount"
					:value="data?.paidAmount || 0"
					icon="streamline:bag-dollar-solid"
					icon-bg-class="bg-success-main"
					icon-class="icon"
					gradient-class="bg-gradient-start-4"
					is-currency
					:change-percent="changePercent"
				/>
			</div>
		</div>
	</div>
</template>

<script setup>
import StatCard from './StatCard.vue'

defineProps({
	branchName: {
		type: String,
		required: true
	},
	data: {
		type: Object,
		default: () => ({})
	},
	changePercent: {
		type: Number,
		default: null
	}
})
</script>

