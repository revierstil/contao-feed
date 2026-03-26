<template>
	<picture v-if="sources">
		<source
			v-for="source in sources"
			:key="source.srcset"
			:srcset="source.srcset"
			:sizes="source.sizes"
			:media="source.media"
			:type="source.type"
			:width="source.width"
			:height="source.height"
		>

		<img
			:src="img.src"
			:srcset="img.srcset"
			:sizes="img.sizes"
			:width="img.width"
			:height="img.height"
			:alt="altOrTitle"
			:loading="img.loading"
			:title="title || ''"
			itemprop="image"
		>
	</picture>
	<img
		v-else
		:src="img.src"
		:srcset="img.srcset"
		:sizes="img.sizes"
		:width="img.width"
		:height="img.height"
		:alt="altOrTitle"
		:loading="img.loading"
		:title="title || ''"
		itemprop="image"
	>
</template>

<script>
export default {
	name: 'PictureDefault',

	props: {
		img: {
			type: Object,
			required: true,
		},
		sources: {
			type: Array,
			required: false,
			default: [],
		},
		title: {
			type: String,
			required: false,
			default: null,
		},
		alt: {
			type: String,
			required: false,
			default: null,
		},
	},
	computed: {
		altOrTitle() {
			return this.alt || this.title || '';
		}
	}
}
</script>
