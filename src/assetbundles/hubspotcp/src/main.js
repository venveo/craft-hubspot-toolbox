import {createApp, ref} from 'vue'
import StoreSelector from './components/StoreSelector.vue'

Craft.HubSpot = {}

Craft.HubSpot.StoreSelector = Garnish.Base.extend({
        init: function (settings) {
            this.setSettings(settings, Craft.HubSpot.StoreSelector.defaults);

            const props = this.settings;
            const app = createApp(StoreSelector)
            const stores = ref(props.stores)
            app.provide('stores', stores)
            app.provide('value', props.value)
            app.provide('name', props.name)
            const vm = app.mount(this.settings.container)
        },
    },
    {
        defaults: {
            container: null,
            stores: [],
            value: null,
            name: null
        }
    });