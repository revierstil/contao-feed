import {acceptHMRUpdate, defineStore} from "pinia";
import type {ConfigModel, ConfigState} from "@/stores/models";
import {configStore} from "@/stores/index";


export const useConfigStore = defineStore("config", {
    state: (): ConfigState => ({
        sorting: [],
        filters: [],
        urls: {
            listing: "",
            create: "",
            like: "",
        },
        options: {},
        requestToken: null,
        initialized: false,
    }),
    actions: {
        setConfigProperties(config: ConfigModel) {
            this.sorting = config.sorting ?? [];
            this.filters = config.filters ?? [];
            this.urls = config.urls ?? {listing: "", create: "", like: ""};
            this.options = config.options ?? {};
            this.initialized = true;
            this.requestToken = config.requestToken ?? null;
        },
    },
    getters: {
        isInitialized: (state: ConfigState) => state.initialized,
    },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useConfigStore, import.meta.hot));
}
