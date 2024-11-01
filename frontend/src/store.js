import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

const state = {
    map_center: {
        lat: 0,
        lng: 0
    },
    zoom: 2,
    show_ids: [],
    markers: [],
    shapes: [],
    grouping_taxonomy: 'waypoint_location_region',
};

const actions = {

};

const mutations = {

    set_grouping_taxonomy(state, n) {
        state.grouping_taxonomy = n;
    },

    add_marker(state, n) {
        let location = n,
            lat = Number(location.meta.lat),
            lng = Number(location.meta.lng),
            content = location.info_window_content;

        state.markers.push({
            position: {
                lat: lat,
                lng: lng
            },
            label: location.post_title,
            open: false,
            content: content,
            post: location
        });
    },

    close_open_info_windows(state) {
        _.each( state.markers, m => {
            m.open = false;
        });
    },

    set_filtered_ids(state, ids) {
        state.show_ids = ids;
    },

    set_map_center(state, coords) {
        state.map_center = coords;
    },

    set_zoom(state, zoom) {
        state.zoom = zoom;
    },

    set_shapes(state, shapes) {
        state.shapes = shapes;
    }
};

const getters = {

    map_center: state => {
        return state.map_center;
    },

    markers: state => {
        return state.markers;
    },

    zoom: state => {
        return state.zoom;
    },

    filtered_markers: state => {
        if (0 === state.show_ids.length) {
            return state.markers;
        }
        let ids = state.show_ids,
            markers = [];
        _.each(state.markers, m => {
            if (-1 !== ids.indexOf(m.post.ID)) {
                markers.push(m);
            }
        });
        return markers;
    },

    grouping_taxonomy: state => {
        return state.grouping_taxonomy;
    }
};



const store = new Vuex.Store({
    state: state,
    actions: actions,
    mutations: mutations,
    getters: getters
});

export default store;