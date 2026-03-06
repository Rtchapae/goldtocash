<template>
	<div id="dash-status">
		<h4>Current status of your last order:</h4>

		<div class="d-flex flex-row justify-content-between align-items-center align-content-center">
			<span
				class="d-flex justify-content-center align-items-center dot"
				:class="{ 'dot-active': currentStatusIndex >= 0, 'dot-inactive': currentStatusIndex < 0 }"
			>
				<i :class="getStatusIcon(0)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 1 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 1, 'dot-inactive': currentStatusIndex < 1 }">
				<i :class="getStatusIcon(1)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 2 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 2, 'dot-inactive': currentStatusIndex < 2 }">
				<i :class="getStatusIcon(2)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 3 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 3, 'dot-inactive': currentStatusIndex < 3 }">
				<i :class="getStatusIcon(3)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 4 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 4, 'dot-inactive': currentStatusIndex < 4 }">
				<i :class="getStatusIcon(4)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 5 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 5, 'dot-inactive': currentStatusIndex < 5 }">
				<i :class="getStatusIcon(5)"></i>
			</span>

			<hr class="flex-fill track-line" :class="{ 'track-line-active': currentStatusIndex >= 6 }">
			<span class="d-flex justify-content-center align-items-center dot" :class="{ 'dot-active': currentStatusIndex >= 6, 'dot-inactive': currentStatusIndex < 6 }">
				<i :class="getStatusIcon(6)"></i>
			</span>
		</div>

		<div class="d-flex flex-row justify-content-between align-items-center">
			<div class="d-flex flex-column align-items-start">
				<span :class="hasOrders && currentStatusIndex === 0 ? 'status-title-large' : 'status-title-inactive'">Kit Requested</span>
			</div>
			<div style="margin-left:-2%;" class="d-flex flex-column justify-content-center">
				<span :class="hasOrders && currentStatusIndex === 1 ? 'status-title-large' : 'status-title-inactive'">In Transit</span>
			</div>
			<div style="margin-left:0%;" class="d-flex flex-column justify-content-center">
				<span :class="hasOrders && currentStatusIndex === 2 ? 'status-title-large' : 'status-title-inactive'">Items Received</span>
			</div>
			<div style="margin-left:0%;" class="d-flex flex-column justify-content-center">
				<span :class="hasOrders && currentStatusIndex === 3 ? 'status-title-large' : 'status-title-inactive'">Appraisal</span>
			</div>
			<div style="margin-left:1%;" class="d-flex flex-column justify-content-center">
				<span :class="hasOrders && currentStatusIndex === 4 ? 'status-title-large' : 'status-title-inactive'">Offer Sent</span>
			</div>
			<div style="margin-left:0%;" class="d-flex flex-column justify-content-center">
				<span :class="hasOrders && currentStatusIndex === 5 ? 'status-title-large' : 'status-title-inactive'">Offer Accepted</span>
			</div>
			<div class="d-flex flex-column align-items-end">
				<span :class="hasOrders && currentStatusIndex === 6 ? 'status-title-large' : 'status-title-inactive'">Paid</span>
			</div>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	currentStatusIndex: {
		type: Number,
		required: true,
	},
	hasOrders: {
		type: Boolean,
		required: true,
	},
})

const getStatusIcon = (index) => {
	if (props.hasOrders && index === props.currentStatusIndex) {
		return 'fas fa-check-circle text-primary fa-2x'
	}
	return 'fas fa-circle'
}
</script>

<style scoped>
.track-line {
	border-top: 2px solid #ddd;
	margin: 0;
}

.track-line-active {
	border-top-color: var(--primary);
}

.dot {
	width: 20px;
	height: 20px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
}

.dot-active {
	color: var(--primary);
}

.dot-inactive {
	color: #ddd;
}

.status-title-large {
	font-weight: bold;
	font-size: 1.1em;
	color: var(--primary);
}

.status-title-inactive {
	color: #999;
	font-size: 0.9em;
}
</style>



