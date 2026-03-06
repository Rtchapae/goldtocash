<template>
	<section id="video-thumbnail">
		<div id="video-hidden-anchor"></div>
		<div id="video-thumbnail-container" role="button" tabindex="0" @click="play" @keydown.enter="play" @keydown.space.prevent="play">
			<div id="video-thumbnail-overlay">
				<div id="video-text" v-if="!isPlaying">
					<h2>Learn how to get the highest
						<br>payout for your gold!
					</h2>
					<img src="/images/video-play-icon.png" alt="Play">
				</div>
				<div id="video-embed-container" v-else>
					<iframe
						id="player"
						frameborder="0"
						allowfullscreen
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
						:title="title"
						width="100%"
						height="100%"
						:src="embedUrl"
					></iframe>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref, computed } from 'vue'

const isPlaying = ref(false)
const title = 'Sell Your Gold Online to Gold to Cash'
const YT_ID = '8wejVPc4lHE'
const embedUrl = computed(() => `https://www.youtube.com/embed/${YT_ID}?autoplay=1&controls=0&rel=0&modestbranding=1&playsinline=1`)

function play() {
	isPlaying.value = true
}
</script>

<style scoped>
#video-hidden-anchor {
	position: relative;
	top: -300px;
	width: 100px;
	height: 0;
	background: transparent;
}

#video-thumbnail-container {
	width: 100%;
	height: 32vw;
	max-height: 700px;
	background-image: url('/images/video-thumbnail.png');
	background-position: center;
	background-repeat: no-repeat;
	background-size: cover;
	color: white;
	text-align: center;
	cursor: pointer;
}

#video-embed-container {
	height: 100%;
}

#video-thumbnail-overlay {
	width: 100%;
	height: 100%;
	max-height: 700px;
	background: rgba(0, 0, 0, 0.3);
}

#video-text {
	padding: min(9vw, 205px) 9vw 9vw;
}

#video-thumbnail-overlay:hover {
	background: rgba(0, 0, 0, 0.4);
}

#video-thumbnail-overlay img:hover {
	opacity: .4;
}

#video-thumbnail-container h2 {
	font-size: 48px;
	font-weight: 100 !important;
}

#video-thumbnail-container img {
	width: 52px;
	height: auto;
	margin-top: 1vw;
}

@media(max-width:1500px) {
	#video-text {
		padding-top: 8vw;
	}
}

@media(max-width:1000px) {
	#video-thumbnail-container {
		height: 45vw;
	}
	#video-text {
		padding: 12vw;
	}
	#video-thumbnail-container #video-text h2 {
		font-size: 5vw;
	}
	#video-thumbnail-container img {
		width: 7vw;
	}
}

@media(max-width:576px) {
	#video-thumbnail-container {
		height: 45vh;
		min-height: 290px;
	}
	#video-text {
		padding: 8vh 0;
		height: 100%;
	}
	#video-thumbnail-container #video-text h2 {
		font-size: 26px;
	}
	#video-text img {
		margin-top: 3vh;
		width: 12vw;
		max-width: 60px;
	}
}
</style>


