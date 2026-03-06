<template>
	<div class="card p-2">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>My Profile</th>
					<th colspan="10" class="text-right">
						<a href="#" @click.prevent="$emit('edit')" title="Edit">
							<i class="fas fa-cog"></i>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Name</td>
					<td colspan="10">{{ userProfile?.name || 'N/A' }}</td>
				</tr>
				<tr>
					<td>Email</td>
					<td colspan="10">{{ userProfile?.email || 'N/A' }}</td>
				</tr>
				<tr>
					<td>Phone</td>
					<td colspan="10">{{ formatPhone(userProfile?.phone) || 'N/A' }}</td>
				</tr>
				<tr>
					<td>Address</td>
					<td colspan="10">
						<span v-if="userProfile?.address">
							{{ userProfile.address }}<br v-if="userProfile.address2"><br v-if="userProfile.address2">
							<span v-if="userProfile.address2">{{ userProfile.address2 }},<br></span>
							{{ userProfile.city }}, {{ userProfile.state }} {{ userProfile.zip }}
						</span>
						<span v-else>N/A</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
defineEmits(['edit'])
defineProps({
	userProfile: { type: Object, required: false, default: null },
	formatPhone: { type: Function, required: true },
})
</script>

<style scoped>
.card {
	border: 1px solid #ddd;
	border-radius: 0.25rem;
	margin-bottom: 1rem;
}
</style>



