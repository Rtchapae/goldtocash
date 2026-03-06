<template>
	<div class="messages-header p-3 border-bottom">
		<div class="d-flex align-items-center">
			<div class="conversation-avatar me-3">
				<i class="fas fa-headset fa-2x"></i>
			</div>
			<div>
				<h5 class="mb-0">{{ SUPPORT_LABELS.NAME }}</h5>
				<small class="text-muted">{{ SUPPORT_LABELS.SUBTITLE }}</small>
			</div>
		</div>
	</div>
</template>

<script setup>
import { SUPPORT_LABELS } from '@/constants/messages'
</script>


