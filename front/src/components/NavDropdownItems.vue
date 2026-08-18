<template>
	<ul :id="id" class="nav-dropdown-menu" :class="[variant, { open }]">
		<li
			v-for="item in items"
			:key="item.label"
			class="nav-dropdown-item"
			:class="{ 'has-submenu': hasChildren(item) }"
			@mouseenter="onEnter(item)"
			@mouseleave="onLeave(item)"
		>
			<a
				v-if="!hasChildren(item)"
				:href="item.href"
			>{{ item.label }}</a>
			<a
				v-else
				href="#"
				class="nav-submenu-trigger"
				@click.prevent="onTriggerClick(item)"
			>{{ item.label }}</a>
			<ul
				v-if="hasChildren(item)"
				class="nav-submenu"
				:class="[variant, { open: openSub === item.label }]"
			>
				<li v-for="child in item.children" :key="child.href">
					<a :href="child.href">{{ child.label }}</a>
				</li>
			</ul>
		</li>
	</ul>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
	id: {
		type: String,
		default: '',
	},
	items: {
		type: Array,
		default: () => [],
	},
	variant: {
		type: String,
		default: 'desktop',
	},
	open: {
		type: Boolean,
		default: false,
	},
})

const openSub = ref(null)

const hasChildren = (item) => Array.isArray(item?.children) && item.children.length > 0

const onEnter = (item) => {
	if (props.variant === 'desktop' && hasChildren(item)) {
		openSub.value = item.label
	}
}

const onLeave = (item) => {
	if (props.variant === 'desktop' && hasChildren(item)) {
		openSub.value = null
	}
}

const onTriggerClick = (item) => {
	if (props.variant !== 'mobile') return
	openSub.value = openSub.value === item.label ? null : item.label
}

watch(() => props.open, (isOpen) => {
	if (!isOpen) {
		openSub.value = null
	}
})
</script>
