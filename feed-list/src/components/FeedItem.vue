<template>
  <div class="feed-item">
    <div class="header">
      <div class="avatar">
        <picture-default v-if=" props.item?.author.avatar" :img="props.item?.author.avatar.picture.img" :sources="props.item?.author.avatar.picture.sources" :alt="props.item?.author.avatar.alt"></picture-default>
        <div v-if="!props.item?.author.avatar" class="avatar-placeholder"></div>
      </div>
      <div class="author">
        <span class="name">
          {{ props.item?.author.firstname }}
          {{ props.item?.author.lastname }}
        </span>
        <span class="time" v-html="fromNow(props.item?.dateCreated)"></span>
      </div>
      <div class="like">
        <span class="like-count" v-html="props.item?.likes"></span>
        <button class="like-button" @click.prevent="feedStore.likeFeed(props.item?.id)"></button>
      </div>
    </div>
    <div class="message">
      {{ props.item?.message }}
      <picture-default v-if="props.item?.image" :img="props.item?.image.picture.img" :sources="props.item?.image.picture.sources" :alt="props.item?.image.alt"></picture-default>
    </div>
  </div>
</template>

<script setup lang="ts">

import type {Feed} from "@/stores/models";
import dayjs from "dayjs";
import PictureDefault from "@/components/partials/PictureDefault.vue";
import {feedStore} from "@/stores";

const props = defineProps({
  item: {
    type: Object as () => Feed,
    required: true
  }
})

const fromNow = (date: string) => {
  return dayjs(date).fromNow();
}

</script>

<style>
</style>
