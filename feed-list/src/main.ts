import {createApp} from "vue";
import App from "@/App.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/de";

import {defaultConfig, plugin} from "@formkit/vue";
import {de} from "@formkit/i18n";
import A11yDialog from 'vue-a11y-dialog'

dayjs.extend(relativeTime);
dayjs.locale("de");

const app = createApp(App);

app.use(
    plugin,
    defaultConfig({
        locales: {de},
        locale: 'de'
    })
);

app.use(A11yDialog);

app.mount("#rs-feed-list");
