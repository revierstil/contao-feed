import {createPinia} from "pinia";
import {useConfigStore} from "@/stores/config";
import {useFeedStore} from "@/stores/feed.ts";

const pinia = createPinia();
export const configStore = useConfigStore(pinia);
export const feedStore = useFeedStore(pinia);
