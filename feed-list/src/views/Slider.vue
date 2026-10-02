<template>
  <div class="feed-slider">
    <Swiper
      :modules="modules"
      :slides-per-view="1"
      :space-between="20"
      navigation
      v-if="isLoaded"
    >
      <SwiperSlide v-for="item in feedStore.feeds" :key="item.id">
        <FeedItem :item="item" />
      </SwiperSlide>
    </Swiper>
    <div class="list-link" v-if="configStore.listUrl && isLoaded">
      <a :href="configStore.listUrl">Alle ansehen</a>
    </div>
    <div class="feed-loading-indicator-overlay" v-if="!isLoaded">
      <div class="feed-loading-indicator"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation } from "swiper/modules";
import FeedItem from "@/components/slider/FeedItem.vue";
import "swiper/css";
import "swiper/css/navigation";
import { configStore, feedStore } from "@/stores";
import { storeToRefs } from "pinia";
import { computed } from "vue";
const { isInitialized } = storeToRefs(configStore);

const modules = [Navigation];

const isLoaded = computed(() => !feedStore.loading && isInitialized);
</script>

<style scoped></style>
