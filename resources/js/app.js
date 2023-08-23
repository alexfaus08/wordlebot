import './bootstrap';
import {createApp} from 'vue';
import '../css/app.css';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

import App from './views/App.vue';

const app = createApp(App);
app.component('VueDatePicker', VueDatePicker);

app.mount('#app');
