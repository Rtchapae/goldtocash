<template>
  <div>
    <template v-if="show">
      <slot />
    </template>
    <template v-else-if="$slots.fallback">
      <slot name="fallback" />
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const show = ref(false)

onMounted(() => {
  show.value = true
})
</script>
