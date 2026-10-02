<template>
  <List v-if="configStore.mode === 'list' && isInitialized" />
  <Slider v-if="configStore.mode === 'slider' && isInitialized" />
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { configStore, feedStore } from "@/stores";
import { onMounted, watch } from "vue";
import List from "@/views/List.vue";
import Slider from "@/views/Slider.vue";

const { isInitialized } = storeToRefs(configStore);
const { currentPage, sorting, filters } = storeToRefs(feedStore);

onMounted(function () {
  const configElement = document.getElementById("rs-feed-list-config");
  if (!configElement) {
    console.log("Missing config element");
    return;
  }

  const config = JSON.parse(configElement.textContent || "");
  if (!config) {
    console.log("Config element is empty");
    return;
  }

  configStore.setConfigProperties(config);
});

watch(isInitialized, (value) => {
  if (value === false) {
    return;
  }

  feedStore.loadFeeds();
});
</script>

<style></style>
