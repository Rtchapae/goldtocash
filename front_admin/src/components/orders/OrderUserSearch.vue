<template>
	<div class="mb-4">
		<label class="form-label fw-semibold mb-3">Search User by Email or Phone</label>
		<div class="row">
			<div class="col-md-6 mb-3">
				<input
					:value="modelValue"
					type="text"
					class="form-control"
					placeholder="Enter email or phone to search user"
					@input="onInput"
				/>
				<div v-if="results.length > 0" class="mt-2">
					<div
						v-for="user in results"
						:key="user.id"
						class="list-group-item list-group-item-action cursor-pointer"
						@click="onSelectUser(user)"
					>
						<div class="fw-semibold">{{ user.name || 'N/A' }}</div>
						<div class="text-sm text-secondary">
							{{ user.email }} | {{ user.phone || 'N/A' }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	modelValue: {
		type: String,
		default: ''
	},
	results: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits(['update:modelValue', 'search', 'select'])

const onInput = (event) => {
	const value = event.target.value
	emit('update:modelValue', value)
	emit('search', value)
}

const onSelectUser = (user) => {
	emit('select', user)
}
</script>



