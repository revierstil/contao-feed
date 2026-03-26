import {acceptHMRUpdate, defineStore} from "pinia";
import type {FeedsState} from "@/stores/models";
import {fetchFeed, likeFeed} from "@/api";


export const useFeedStore = defineStore("feed", {
    state: (): FeedsState => ({
        feeds: [],
        pagination: null,
        currentPage: 1,
        filterElements: [],
        filters: {},
        sorting: null,
        loading: false,
    }),
    actions: {
        async loadFeeds(disableState = false): Promise<void> {
            if (this.loading) {
                return;
            }

            if (!disableState) {
                this.loading = true;
            }

            try {
                const response = await fetchFeed(this.currentPage, this.filters, this.sorting)
                this.feeds = response.data.items;
                this.pagination = response.data.pagination;
                this.filterElements = response.data.filters;
            } catch (error) {
                alert('Es ist leider ein Fehler aufgetreten, bitte versuchen Sie es erneut.')
            }

            this.loading = false;
        },
        async likeFeed(feedId: number): Promise<void> {
            try {
                await likeFeed(feedId)
                await this.loadFeeds(true);
            } catch (error) {
                alert('Es ist leider ein Fehler aufgetreten, bitte versuchen Sie es erneut.')
            }
        }
    }
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useFeedStore, import.meta.hot));
}
