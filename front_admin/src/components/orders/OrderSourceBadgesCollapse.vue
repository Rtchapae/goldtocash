<template>
	<div class="order-source-cell d-flex align-items-start flex-wrap gap-1">
		<template v-if="!normalizedBadges.length">
			<span class="text-muted">—</span>
		</template>
		<template v-else-if="expanded">
			<span
				v-for="badge in normalizedBadges"
				:key="badge.key + String(badge.value)"
				class="badge bg-secondary text-white text-monospace px-2 py-1 fw-normal"
				:title="String(badge.key)"
			>
				{{ badge.value }}
			</span>
			<button
				v-if="normalizedBadges.length > 1"
				type="button"
				class="btn btn-link btn-sm p-0 order-source-toggle align-self-center"
				:title="'Hide'"
				aria-label="Hide all source fields"
				@click="expanded = false"
			>
				<iconify-icon icon="solar:alt-arrow-up-linear" class="icon" />
			</button>
		</template>
		<template v-else>
			<span
				class="badge bg-secondary text-white text-monospace px-2 py-1 fw-normal text-truncate order-source-first-badge"
				:title="firstTitle"
			>
				{{ firstBadge.value }}
			</span>
			<button
				v-if="normalizedBadges.length > 1"
				type="button"
				class="btn btn-link btn-sm p-0 order-source-toggle align-self-center"
				:title="`Show all (${normalizedBadges.length})`"
				aria-label="Show all source fields"
				@click="expanded = true"
			>
				<iconify-icon icon="solar:alt-arrow-down-linear" class="icon" />
			</button>
		</template>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
	badges: {
		type: Array,
		default: () => []
	}
})

const expanded = ref(false)

const normalizedBadges = computed(() =>
	(Array.isArray(props.badges) ? props.badges : []).filter((b) => b && String(b.value ?? '').length)
)

const firstBadge = computed(() => normalizedBadges.value[0] || { key: '', value: '' })

const firstTitle = computed(() => {
	const b = firstBadge.value
	return `${b.key}: ${b.value}`
})
</script>

<style scoped>
.order-source-cell {
	min-width: 0;
	max-width: 100%;
}

.order-source-first-badge {
	max-width: min(10rem, 100%);
	display: inline-block;
	vertical-align: middle;
}

.order-source-toggle {
	line-height: 1;
	min-width: 1.25rem;
}

.order-source-toggle .icon {
	font-size: 1.1rem;
	vertical-align: middle;
}
</style>
