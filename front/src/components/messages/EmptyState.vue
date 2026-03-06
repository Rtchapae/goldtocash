<template>
	<div class="card p-5 text-center">
		<i class="fas fa-comments fa-4x text-muted mb-3"></i>
		<h5>{{ EMPTY_STATES.SELECT_CONVERSATION }}</h5>
		<p class="text-muted">{{ EMPTY_STATES.SELECT_CONVERSATION_DESCRIPTION }}</p>
	</div>
</template>

<script setup>
import { EMPTY_STATES } from '@/constants/messages'
</script>


