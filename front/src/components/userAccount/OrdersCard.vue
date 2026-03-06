<template>
	<div class="card p-2">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Date Created</th>
					<th>Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="order in orders" :key="order.id">
					<td>{{ order.id }}</td>
					<td>{{ formatDate(order.created_at) }}</td>
					<td>{{ formatAmount(order.amount) }}</td>
					<td>
						<label class="badge badge-danger">{{ getStatusLabel(order.status) }}</label>
					</td>
				</tr>

				<tr v-if="orders.length === 0">
					<td colspan="4" class="text-center">No orders found</td>
				</tr>

				<tr>
					<td colspan="4" class="text-center">
						<a v-if="orders.length > 0" href="/user/orders">View All Orders</a>
						<a v-else href="#" @click.prevent="$emit('request-kit')" title="Request Kit">Request Kit</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
defineProps({
	orders: { type: Array, required: true },
	formatDate: { type: Function, required: true },
	formatAmount: { type: Function, required: true },
	getStatusLabel: { type: Function, required: true },
})

defineEmits(['request-kit'])
</script>

<style scoped>
.badge-danger {
	background-color: #dc3545;
	color: white;
	padding: 0.25em 0.5em;
	border-radius: 0.25rem;
}

.card {
	border: 1px solid #ddd;
	border-radius: 0.25rem;
	margin-bottom: 1rem;
}
</style>


