/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");
require("admin-lte");

window.Vue = require("vue");

import { Form, HasError, AlertError } from "vform";
import moment from "moment";
import VueProgressBar from "vue-progressbar";
import VueRouter from "vue-router";
import swal from "sweetalert2";
import Gate from "./gate";

Vue.prototype.$gate = new Gate(window.user);

window.Form = Form;
window.Swal = swal;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.use(VueRouter);

const routes = [
    {
        path: "/dash",
        component: require("./components/dash.vue").default
    },
    {
        path: "/profile",
        component: require("./components/profile.vue").default
    },
    {
        path: "/users",
        component: require("./components/users.vue").default
    },
    {
        path: "/system",
        component: require("./components/system.vue").default
    },
    {
        path: "*",
        component: require("./components/404.vue").default
    }
];

Vue.filter("upper", function(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter("datey", function(createdat) {
    return moment(createdat).format("MMMM Do YYYY");
});

const router = new VueRouter({
    mode: "history",
    routes
});

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: toast => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    }
});
window.Toast = Toast;

window.events = new Vue();
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component(
    "passport-clients",
    require("./components/passport/Clients.vue").default
);
Vue.component("pagination", require("laravel-vue-pagination"));
Vue.component(
    "passport-authorized-clients",
    require("./components/passport/AuthorizedClients.vue").default
);

Vue.component(
    "passport-personal-access-tokens",
    require("./components/passport/PersonalAccessTokens.vue").default
);
Vue.component("not-found", require("./components/404.vue").default);

Vue.use(VueProgressBar, {
    color: "rgb(149, 97, 226)",
    failedcolor: "red",
    height: "5px"
});
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
    router,
    data: {
        search: ""
    },

    methods: {
        searchit: _.debounce(() => {
            events.$emit("searching");
        }, 1000),

        printfunction(){
            window.print();
        }
    }
});
