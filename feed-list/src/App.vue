<template>
  <div class="feed-controls" v-if="isInitialized">
    <FormKit
      type="form"
      autocomplete="off"
      :actions="false"
      id="filter-form"
      name="filter_form"
      class="filter_form"
    >
      <FormKit type="group" v-model="feedStore.filters">
        <FormKit
          type="select"
          name="location"
          :options="locationOptions()"
          :sections-schema="schemas.select"
          label="Standorte"
        >
        </FormKit>
      </FormKit>
      <FormKit
        type="select"
        name="sorting"
        v-model="feedStore.sorting"
        :options="sortingOptions()"
        :sections-schema="schemas.select"
        label="Sortierung"
      >
      </FormKit>
    </FormKit>
    <button class="create-toggler" type="button" @click="openDialog">
      Beitrag
    </button>
  </div>
  <div class="feed-list">
    <feed-item :item="item" v-for="item in feedStore.feeds" :key="item.id"></feed-item>
    <pagination v-if="feedStore.pagination && feedStore.pagination.total > 1" :pagination="feedStore.pagination" :current-page="feedStore.currentPage"
                @change-page="changePage"/>
    <div class="feed-loading-indicator-overlay" v-if="feedStore.loading || !isInitialized">
      <div class="feed-loading-indicator"></div>
    </div>
  </div>
  <A11yDialog id="create-dialog" @dialog-ref="assignDialogRef" v-if="isInitialized">

    <FormKit
      type="form"
      autocomplete="off"
      :actions="false"
      id="create-form"
      name="create_form"
      class="create_form"
      v-model="createData"
      @submit="submitFeed()"
    >
      <FormKit
        type="select"
        name="location"
        label="Standort"
        :options="configStore.options.location.options || []"
      >
      </FormKit>
      <FormKit
        type="textarea"
        name="message"
        label="Nachricht"
      >
      </FormKit>
      <FormKit
        type="file"
        name="image"
        id="image"
        label="Nachricht"
      >
      </FormKit>
      <FormKit type="submit" label="Speichern"/>
    </FormKit>
  </A11yDialog>
</template>

<script setup lang="ts">

import {storeToRefs} from "pinia";
import {configStore, feedStore} from "@/stores";
import {nextTick, onMounted, ref, watch} from "vue";
import FeedItem from "@/components/FeedItem.vue";
import Pagination from "@/components/partials/Pagination.vue";
import {schemas} from "@/inputs/schemas.ts";
import {A11yDialog} from "vue-a11y-dialog";
import {createFeed} from "@/api";
import {getNode} from '@formkit/core'
import axios from "axios";
import {setErrors} from "@formkit/vue";

const {isInitialized} = storeToRefs(configStore);
const {currentPage, sorting, filters} = storeToRefs(feedStore);
const dialog = ref(null);
const createData = ref({})


onMounted(function () {
  const configElement = document.getElementById("rs-feed-list-config")
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
})

watch(sorting, (value, oldValue) => {
  if (value === oldValue) {
    return;
  }

  currentPage.value = 1;

  feedStore.loadFeeds();
})

watch(filters, (value, oldValue) => {
  if (value === oldValue) {
    return;
  }

  currentPage.value = 1;

  feedStore.loadFeeds();
})

const changePage = (page: number) => {
  currentPage.value = page;

  nextTick(() => {
    feedStore.loadFeeds();
  })
}

const locationOptions = () => {
  if(!feedStore.filterElements.location) {
    return [];
  }

  const options = feedStore.filterElements.location.options;
  options.unshift({label: "Alle Standorte", value: ""});

  return options;
}

const sortingOptions = () => {
  let sortingConfig = configStore.sorting;
  if (!sortingConfig) {
    return [];
  }

  let options = [];

  sortingConfig.forEach((sortingElement) => {
    if (sortingElement.default && sorting.value === null) {
      sorting.value = sortingElement.value;
    }

    options.push({label: sortingElement.label, value: sortingElement.value});
  });

  return options;
}

async function submitFeed() {

  try {
    const response = await createFeed(createData.value);
    await feedStore.loadFeeds(true);

    if (dialog.value) {
      dialog.value.hide();
    }

    nextTick(() => {
      createData.value = {};

      const file = getNode('image')
      file?.reset()

      const form = getNode('create-form')
      form?.reset()
    });
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      const data = error.response.data;

      if (data.message) {
        setErrors('create-form', [], data.message.fields)
      } else {
        alert('Es ist leider ein Fehler aufgetreten, bitte versuchen Sie es erneut.')
      }
    }
  }
}

function assignDialogRef(dialogRef) {
  dialog.value = dialogRef;

  dialog.value.$el.addEventListener('show', function (event) {
    toggleBodyClass();
  });

  dialog.value.$el.addEventListener('hide', function (event) {
    toggleBodyClass();
  });
}

function openDialog() {
  if (dialog.value) {
    dialog.value.show();
  }
}

function toggleBodyClass() {
  let body = document.getElementsByTagName('body');

  if (body.length === 0 || body[0] === undefined) {
    return;
  }

  let bodyItem = body[0];

  if (bodyItem.classList.contains('feed-list-create-open')) {
    bodyItem.classList.remove('feed-list-create-open');
    return;
  }

  bodyItem.classList.add('feed-list-create-open');
}

</script>

<style>
</style>
