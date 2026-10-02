import {acceptHMRUpdate, defineStore} from "pinia";
import type {ConfigModel, ConfigState} from "@/stores/models";


export const useConfigStore = defineStore("config", {
  state: (): ConfigState => ({
    sorting: [],
    filters: [],
    urls: {
      listing: "",
      manage: "",
      like: "",
      delete: "",
    },
    options: {},
    requestToken: null,
    initialized: false,
    mode: "list",
    listUrl: null,
  }),
  actions: {
    setConfigProperties(config: ConfigModel) {
      this.sorting = config.sorting ?? [];
      this.filters = config.filters ?? [];
      this.urls = config.urls ?? {
        listing: "",
        manage: "",
        like: "",
        delete: "",
      };
      this.options = config.options ?? {};
      this.initialized = true;
      this.requestToken = config.requestToken ?? null;
      this.mode = config.mode ?? "list";
      this.listUrl = config.listUrl ?? null;
    },
  },
  getters: {
    isInitialized: (state: ConfigState) => state.initialized,
  },
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useConfigStore, import.meta.hot));
}
