require('./bootstrap');
require('alpinejs');
require('./backend.js');

import $ from 'jquery';
import select2 from 'select2';
import InfiniteLoading from 'vue-infinite-loading';

window.Vue = require('vue');
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
// const app = new Vue({
//     el: '#app',
// });

Vue.use(InfiniteLoading);

if ($('#notification_admin').length > 0) {
    const notifcationAdmin = new Vue({
        el: '#notification_admin',
        data: {
            notifications: [],
            processing: false,
            page: 0,
            limit: 10,
            infiniteId: new Date,
            unread_notification_count: 0,
        },
        methods: {

            infiniteAdminNotification($state) {
                if (this.processing === true) {
                    return;
                }
                this.processing = true;
                setTimeout(() => {
                    axios.get(BASE_URL + 'api/admin/notification', {
                        params: {
                            page: this.page,
                            limit: this.limit,
                        },
                    }).then(({ data }) => {
                        this.unread_notification_count = data.notificationCount;
                        if (data.notification.length) {
                            this.page += this.limit;
                            this.limit += 10;
                            this.notifications.push(...data.notification);
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                        this.processing = false;
                    });
                }, 1000);
            },

        }, //method end

        created() {
            this.infiniteAdminNotification();
        },
    })
}