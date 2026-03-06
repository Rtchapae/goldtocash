<template>
	<div class="card p-2">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Verification</th>
					<th colspan="10" class="text-right"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Payment Method</td>
					<td>
						<i v-if="!userProfile?.payment_method" class="fas fa-ban text-danger"></i>
						<span v-else>{{ userProfile.payment_method }}</span>
					</td>
					<td></td>
					<td>
						<a href="#" @click.prevent="$emit('edit-payment-method')" title="Edit">
							<i class="fas fa-cog"></i>
						</a>
					</td>
				</tr>
				<tr>
					<td>Government ID</td>
					<td>
						<i v-if="!userProfile?.government_id" class="fas fa-ban text-danger"></i>
						<span v-else>Verified</span>
					</td>
					<td></td>
					<td>
						<a href="#" @click.prevent="$emit('edit-docs')" title="Edit">
							<i class="fas fa-cog"></i>
						</a>
					</td>
				</tr>
				<tr>
					<td>Date of Birth</td>
					<td>
						<i v-if="!userProfile?.date_of_birth" class="fas fa-ban text-danger"></i>
						<span v-else>{{ formatDate(userProfile.date_of_birth) }}</span>
					</td>
					<td></td>
					<td>
						<a href="#" @click.prevent="$emit('edit-date-of-birth')" title="Edit">
							<i class="fas fa-cog"></i>
						</a>
					</td>
				</tr>
				<tr v-if="!isVerificationComplete">
					<td colspan="4" class="text-center text-danger">Please fill in each field</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup>
defineEmits(['edit-payment-method', 'edit-docs', 'edit-date-of-birth'])
defineProps({
	userProfile: { type: Object, required: false, default: null },
	isVerificationComplete: { type: Boolean, required: true },
	formatDate: { type: Function, required: true },
})
</script>

<style scoped>
.card {
	border: 1px solid #ddd;
	border-radius: 0.25rem;
	margin-bottom: 1rem;
}
</style>



