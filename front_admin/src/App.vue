<template>
	<AuthLayout v-if="route?.meta?.layout === 'auth'" />
	<AdminLayout v-else />
	<ToastContainer />
</template>

<script setup>
import { useRoute } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'

const route = useRoute()
</script>

<style scoped>
</style>

