<template>
	<BaseModal id="viewUserProfile" title="View User Profile" @close="$emit('close')">
		<table class="table table-striped">
			<tbody>
				<tr><td>User #</td><td class="user-code">{{ user?.id ?? '' }}</td></tr>
				<tr><td>Name</td><td class="user-name">{{ user?.name ?? '' }}</td></tr>
				<tr><td>Email</td><td class="user-login">{{ user?.email ?? '' }}</td></tr>
				<tr><td>Phone</td><td class="user-phone">{{ user?.phone ?? '' }}</td></tr>
				<tr><td>Orders</td><td class="user-orders">{{ user?.ordersCount ?? '' }}</td></tr>
				<tr><td>Address</td><td class="user-address">{{ user?.address ?? '' }}</td></tr>
				<tr><td>Date Created</td><td class="user-create">{{ user?.createdAt ?? '' }}</td></tr>
				<tr><td>Last Update</td><td class="user-update">{{ user?.updatedAt ?? '' }}</td></tr>
			</tbody>
		</table>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	user: { type: Object, default: null },
})

defineEmits(['close'])
</script>



