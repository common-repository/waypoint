import './helpers/maybe-polyfill';
import Vue from 'vue'
import WaypointMap from './WaypointMap.vue'
import WaypointMapLocations from './WaypointMapLocations.vue'
import WaypointMapZipSearch from './WaypointMapZipSearch.vue'

import * as store from './store'

let $ = jQuery;
//$('.waypoint-map-container').each(function() {
    new Vue({
        components: {
            WaypointMap,
        },
        //el: '#' + $(this).attr('id')
        el: '.fl-page-content'
    });
//});


new Vue({
    components: {
        WaypointMapLocations
    },
    el: '#waypoint-map-locations-container',
});

new Vue({
    components: {
        WaypointMapZipSearch
    },
    el: '#waypoint-map-locations-zip-search'
});