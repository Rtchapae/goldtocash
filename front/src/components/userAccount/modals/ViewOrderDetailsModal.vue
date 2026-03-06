<template>
	<BaseModal id="viewOrderDetails" title="View Order Details" @close="$emit('close')">
		<table class="table table-striped">
			<tbody>
				<tr><td>Name</td><td class="user-name">{{ order?.userName ?? '' }}</td></tr>
				<tr><td>Email</td><td class="user-login">{{ order?.email ?? '' }}</td></tr>
				<tr><td>Phone</td><td class="user-phone">{{ order?.phone ?? '' }}</td></tr>
				<tr><td>Address</td><td class="order-address">{{ order?.address ?? '' }}</td></tr>
				<tr><td>Order Number</td><td class="order-code">{{ order?.id ?? '' }}</td></tr>
				<tr><td>Date Created</td><td class="order-create">{{ order?.createdAt ?? '' }}</td></tr>
				<tr><td>Amount</td><td class="order-price">{{ order?.amount ?? '' }}</td></tr>
				<tr><td>Status</td><td class="order-status">{{ order?.statusLabel ?? '' }}</td></tr>
			</tbody>
		</table>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	order: { type: Object, default: null },
})

defineEmits(['close'])
</script>



