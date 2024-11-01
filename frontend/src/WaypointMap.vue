<template>
    <div class="map-wrapper" :style="{height: `${map_height}px`}">
        <gmap-map
                :zoom="zoom"
                :center="center"
                :options="{styles: styles}"
                :style="{height: `${map_height}px`}">

            <gmap-info-window v-for="(m, index) in markers"
                              :options="{}" :position="m.position" :opened="m.open"
                               @closeclick="close_info_windows">
                <div v-html="m.content"></div>
            </gmap-info-window>
            <gmap-marker
                    v-for="(m, index) in markers"
                    :key="index"
                    :icon="get_icon(m)"
                    :position="m.position"
                    :clickable="true"
                    :draggable="false"
                    @click="close_info_windows(); /* center=m.position; */ m.open=!m.open;"
            ></gmap-marker>

            <gmap-polygon v-for="shape in shapes"
                          :paths="shape.coordinate_paths"
                          :options="get_shape_options(shape)"
                          @click="click_shape(shape)">
            </gmap-polygon>
        </gmap-map>
    </div>
</template>

<script>

let $ = jQuery;

import Vue from 'vue';
import store from './store';
import * as VueGoogleMaps from 'vue2-google-maps';

if ( ! window.waypoint.google.api_key ) {
    console.log( 'Error: No Google API key set for Waypoint Plugin.' );
}

Vue.use(VueGoogleMaps, {
    load: {
        key: window.waypoint.google.api_key
    }
});

export default {
    name: 'waypoint-map',

    props: [
        'mapId'
    ],

    data() {
        return {
            styles: {},
            map_height: window.waypoint.map_defaults.height
        }
    },

    computed: {
        markers() {
            return store.getters.filtered_markers;
        },

        center() {
            return store.getters.map_center;
        },

        zoom() {
            return store.getters.zoom;
        },

        shapes() {
            return store.state.shapes;
        }
    },

    methods: {
        set_styles(value) {
            this.styles = value;
        },

        set_zoom(value) {
            store.commit('set_zoom', value);
        },

        set_center( lat, lng ) {
            let coords = {
                lat: Number(lat),
                lng: Number(lng)
            };
            store.commit('set_map_center', coords);
        },

        close_info_windows() {
            _.each(this.markers, m => {
                m.open = false;
            });
        },

        show_pin(marker, event) {
            event.preventDefault();
            this.close_info_windows();
            marker.open = true;
        },

        get_icon(location) {
            let url = '',
                meta = location.post.meta,
                pin_type = meta.map_pin_type,
                map_specific_pins = meta.map_specific_pins;

            // Check if this location has a map specific pin set.
            if ( null !== map_specific_pins && 0 !== map_specific_pins.length ) {
                _.each( map_specific_pins, item => {
                    if ( Number( item.map_id ) === Number( this.mapId ) ) {
                        if ( item.image_url !== '' ) {
                            url = item.image_url;
                        }
                    }
                });
                if ( url ) {
                    return url;
                }
            }

            if ( 'default' !== pin_type.toLowerCase() && "" !== pin_type ) {
                url = meta.map_pin_url;
                return url;
            }
            pin_type = window.waypoint.map_defaults.map_pin_type;
            if ( 'default' !== pin_type && '' !== pin_type ) {
                url = window.waypoint.map_defaults.map_pin_url;
                return url;
            }
            return url;
        },

        preload_locations() {

            let style = window.waypoint.map_defaults.style,
                win_width = $(window).width();

            // Set global map styles.
            if (style.length !== 0) {
                this.styles = JSON.parse(window.waypoint.map_defaults.style);
            }

            if (this.mapId) {

                let data = window[`waypoint_map_${this.mapId}`],
                    map_defaults = window.waypoint.map_defaults,
                    desktop_lat = Number(map_defaults.desktop.lat),
                    desktop_lng = Number(map_defaults.desktop.lng),
                    desktop_zoom = Number(map_defaults.desktop.zoom),
                    mobile_lat = Number(map_defaults.mobile.lat),
                    mobile_lng = Number(map_defaults.mobile.lng),
                    mobile_zoom = Number(map_defaults.mobile.zoom),
                    type = String( data.map_center_type ).toLowerCase(),
                    lat, lng, zoom;

                // This map has custom map center overrides.
                if ('custom' === type.toLowerCase()) {
                    desktop_lat = Number(data.desktop.lat);
                    desktop_lng = Number(data.desktop.lng);
                    desktop_zoom = Number(data.desktop.zoom);
                    mobile_lat = Number(data.mobile.lat);
                    mobile_lng = Number(data.mobile.lng);
                    mobile_zoom = Number(data.mobile.zoom);
                }

                if (win_width > 768) {
                    lat = desktop_lat;
                    lng = desktop_lng;
                    zoom = desktop_zoom;
                } else {
                    lat = mobile_lat;
                    lng = mobile_lng;
                    zoom = mobile_zoom;
                }

                if (0 !== lat.length && 0 !== lng.length) {
                    this.set_center(lat, lng);
                }

                if (0 !== zoom.length) {
                    this.set_zoom(zoom);
                }

                // Generate locations.
                _.each(data.locations, location => {
                    store.commit('add_marker', location);
                });
            }
        },

        /*
        preload_shapes() {
            let data = window[`waypoint_map_${this.mapId}`],
                shapes = data.shapes;
            store.commit('set_shapes', shapes);
        }
        */
    },

    mounted() {
        this.preload_locations();

        // Todo: move shapes to extensible module.
        // this.preload_shapes();
    }
}
</script>

<style lang="scss">
  .vue-map {
    width: 100%;
    display: block;
  }

  .locations-list {
    margin-bottom: 20px;
  }
</style>