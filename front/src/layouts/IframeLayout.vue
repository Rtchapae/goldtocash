<template>
	<div class="iframe-layout">
		<main>
			<slot />
		</main>
	</div>
</template>

<script setup>
</script>

<style scoped>
.iframe-layout {
	min-height: 100vh;
	background: #fff;
}

main {
	padding: 0;
}
</style>

