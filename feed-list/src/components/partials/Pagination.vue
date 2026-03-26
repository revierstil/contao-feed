<template>
  <nav class="pagination" aria-label="Feednavigation">
    <ul class="pagination__list">
      <li class="pagination__item">
        <button
          class="pagination__prev"
          :disabled="!pagination?.hasPreviousPage"
          @click="emit('changePage', (pagination?.currentPage ?? 0) - 1)"
        >
          Zurück
        </button>
      </li>

      <li
        v-for="page in pageRange"
        :key="page"
        class="pagination__item"
      >
        <button
          class="pagination__page"
          :class="{ 'pagination__page--active': page === pagination?.currentPage }"
          :aria-current="page === pagination?.currentPage ? 'page' : undefined"
          :disabled="pagination?.currentPage === page"
          @click="emit('changePage', page)"
        >
          {{ page }}
        </button>
      </li>

      <li class="pagination__item">
        <button
          class="pagination__next"
          :disabled="!pagination?.hasNextPage"
          @click="emit('changePage', (pagination?.currentPage ?? 0) + 1)"
        >
          Weiter
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">
import type {Pagination} from "@/stores/models";
import {computed} from "vue";

const props = defineProps<{
  pagination: Pagination | null;
}>();

const emit = defineEmits<{
  changePage: [page: number];
}>();

const pageRange = computed(() => {
  const range: number[] = [];
  for (let i = 1; i <= props.pagination.pages; i++) {
    range.push(i);
  }
  return range;
});
</script>
