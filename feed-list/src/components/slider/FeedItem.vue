<template>
  <div class="feed-item">
    <div class="header">
      <div class="avatar">
        <picture-default
          v-if="props.item?.author.avatar"
          :img="props.item?.author.avatar.picture.img"
          :sources="props.item?.author.avatar.picture.sources"
          :alt="props.item?.author.avatar.alt"
        ></picture-default>
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
        <button
          class="like-button"
          @click.stop="feedStore.likeFeed(props.item?.id)"
        ></button>
      </div>
    </div>
    <div class="message">
      <p>{{ props.item?.message }}</p>
      <button
        v-if="props.item?.image"
        class="attachment-button"
        @click.stop="openDialog"
      >
        <picture-default
          :img="props.item?.image.picture.img"
          :sources="props.item?.image.picture.sources"
          :alt="props.item?.image.alt"
        ></picture-default>
        <span class="attachment-button-text">Anhang ansehen</span>
      </button>
    </div>
  </div>
  <A11yDialog
    :id="`attachment-dialog-${props.item?.id}`"
    @dialog-ref="assignDialogRef"
    v-if="props.item?.image"
  >
    <template #default>
      <picture-default
        v-if="props.item?.image"
        :img="props.item?.image.picture.img"
        :sources="props.item?.image.picture.sources"
        :alt="props.item?.image.alt"
      ></picture-default>
    </template>
  </A11yDialog>
</template>

<script setup lang="ts">
import type { Feed } from "@/stores/models";
import dayjs from "dayjs";
import PictureDefault from "@/components/partials/PictureDefault.vue";
import { configStore, feedStore } from "@/stores";
import { A11yDialog } from "vue-a11y-dialog";
import { ref } from "vue";

const dialog = ref(null);

const props = defineProps({
  item: {
    type: Object as () => Feed,
    required: true,
  },
});

const fromNow = (date: string) => {
  return dayjs(date).fromNow();
};

function assignDialogRef(dialogRef) {
  dialog.value = dialogRef;

  dialog.value.$el.addEventListener("show", function (event) {
    toggleBodyClass();
  });

  dialog.value.$el.addEventListener("hide", function (event) {
    toggleBodyClass();
    resetForm();
  });
}

function openDialog() {
  if (dialog.value) {
    dialog.value.show();
  }
}

function toggleBodyClass() {
  let body = document.getElementsByTagName("body");

  if (body.length === 0 || body[0] === undefined) {
    return;
  }

  let bodyItem = body[0];

  if (bodyItem.classList.contains("feed-list-create-open")) {
    bodyItem.classList.remove("feed-list-create-open");
    return;
  }

  bodyItem.classList.add("feed-list-create-open");
}
</script>

<style></style>
