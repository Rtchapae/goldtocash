<template>
	<BaseModal id="getAccountModal" title="" @close="$emit('close')">
		<div class="text-center">
			<div class="m-5">
				<h3>Create <span style="color: var(--primary);">an Account</span></h3>
				<p>
					You will need an&nbsp;account to&nbsp;check the status of&nbsp;your appraisal process, track your package,
					respond to&nbsp;our offer, view and edit your personal and payment information.
				</p>
				<h4>Username: <span>{{ username }}</span></h4>
				<p>Your username is&nbsp;the email address you provided.</p>
				<h4 id="AccountModalPass">Password: <span>{{ password }}</span></h4>
				<p>Here is your secure password.</p>
				<p>For your record we have sent you an email containing your password and username.</p>
				<button id="submitGetPass" class="btn btn-primary" @click="$emit('confirm')">Create My&nbsp;Account</button>
			</div>
		</div>
	</BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
	username: { type: String, default: '' },
	password: { type: String, default: '' },
})

defineEmits(['close', 'confirm'])
</script>



