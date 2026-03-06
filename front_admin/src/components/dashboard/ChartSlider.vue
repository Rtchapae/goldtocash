<template>
	<div class="col-xxl-4 d-flex flex-column gap-4">
		<h6 class="fw-semibold mb-0">{{ title }}</h6>
		<div class="card h-100 radius-8 border-0 position-relative">
			<div class="card-body p-24">
				<h6 class="mb-2 fw-bold text-lg">{{ currentTitle }}</h6>
			</div>
			<div class="chart-slider-container position-relative">
				<div v-if="isLoading || !chartsInitialized" class="chart-loading-overlay">
					<div class="loading-dots">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</div>
				<div 
					class="chart-slider-wrapper" 
					:style="{ '--chart-slide-offset': currentSlide }"
				>
					<div 
						v-for="(chartId, index) in chartIds" 
						:key="chartId"
						class="chart-slide"
					>
						<div :ref="el => setChartRef(el, index)"></div>
					</div>
				</div>
				<button 
					class="chart-nav-btn chart-nav-prev" 
					@click="prevSlide"
					:disabled="currentSlide === 0"
				>
					<i class="fa fa-chevron-left"></i>
				</button>
				<button 
					class="chart-nav-btn chart-nav-next" 
					@click="nextSlide"
					:disabled="currentSlide === titles.length - 1"
				>
					<i class="fa fa-chevron-right"></i>
				</button>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
	title: {
		type: String,
		required: true
	},
	titles: {
		type: Array,
		required: true
	},
	chartIds: {
		type: Array,
		required: true
	},
	isLoading: {
		type: Boolean,
		default: false
	},
	chartsInitialized: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['slide-change'])

const currentSlide = ref(0)
const chartRefs = ref([])

const currentTitle = computed(() => {
	return props.titles[currentSlide.value] || ''
})

const setChartRef = (el, index) => {
	if (el) {
		chartRefs.value[index] = el
	}
}

const prevSlide = () => {
	if (currentSlide.value > 0) {
		currentSlide.value--
		emit('slide-change', currentSlide.value)
	}
}

const nextSlide = () => {
	if (currentSlide.value < props.titles.length - 1) {
		currentSlide.value++
		emit('slide-change', currentSlide.value)
	}
}

defineExpose({
	currentSlide,
	chartRefs
})
</script>


