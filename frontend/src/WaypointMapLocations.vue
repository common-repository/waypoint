<template>
    <div class="map-location-list-wrap">
        <!--
        <div class="">
            <div v-for="m in markers" :key="m.post.ID">
                <a href="#" @click="show_on_map( m, $event )">
                    {{ m.label }}
                </a>
            </div>
        </div>
        -->

        <div v-for="(t, i) in taxonomy_terms" class="panel-container" :key="t.term_id">
            <div class="panel-header" @click="toggle_panel(i)">
                {{ t.name }}
            </div>

            <div class="panel-body" :ref="`map_list_${i}`">
                <div v-for="m in get_markers_by_term_id(t.term_id)" :key="m.post.ID">
                    <a href="#" @click="show_on_map(m, $event)">
                        {{ m.label }}
                    </a>
                </div>
            </div>
        </div>

        <!--
        <div v-if="0 === taxonomy_terms.length*/">
            <div class="panel-container">
                <div class="panel-header">
                    Locations
                </div>
                <div class="panel-body">
                    <div v-for="m in markers" :key="m.post.ID">
                        <a href="#" @click="show_on_map(m, $event)">
                            {{ m.label }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
</template>

<script>
    import Vue from 'vue';
    import store from './store';
    let $ = jQuery;

    export default {
        name: 'waypoint-map-locations',

        props: [
            'mapId'
        ],

        data() {
            return {
                locations: []
            }
        },
        methods: {
            scroll_to_map() {
                let map = $('.map-wrapper'),
                    html = $('html, body');
                html.stop();
                html.animate({
                    scrollTop: map.offset().top
                }, 1000);
            },

            show_on_map( marker, e ) {
                e.preventDefault();
                store.commit('close_open_info_windows');
                marker.open = true;
                this.scroll_to_map();
            },

            get_taxonomy_terms_from_locations( taxonomy ) {
                let terms = [],
                    term_ids = [];
                _.each(this.markers, m => {
                    let post = m.post,
                        cur_terms = post.terms;
                    _.each(cur_terms, t => {
                        if ( taxonomy === t.taxonomy) {
                            if ( -1 === term_ids.indexOf( t.term_id ) ) {
                                terms.push(t);
                                term_ids.push(t.term_id);
                            }
                        }
                    });
                });
                return terms;
            },

            get_markers_by_term_id(term_id) {
                let output = [],
                    post_ids = [];
                _.each(this.markers, m => {
                    _.each(m.post.terms, t => {
                        if ( term_id === t.term_id && -1 === post_ids.indexOf( m.post.ID )) {
                            output.push(m);
                            post_ids.push(m.post.ID);
                        }
                    });
                });
                return this.sort_alphabetically( output, 'label' );
            },

            /**
             * Sort an object alphabetically.
             * @param elements
             * @param key
             */
            sort_alphabetically( elements, key ) {
                return elements.sort((a, b) => {
                    return (a[key] > b[key]) ? 1 : ((b[key] > a[key]) ? -1 : 0);
                });
            },

            toggle_panel(i) {
                let el = $(this.$refs[`map_list_${i}`]);

                _.each( $('.map-location-list-wrap .panel-body'), p => {
                    if ( el[0] != $(p)[0] ) {
                        $(p).slideUp();
                    }
                });

                el.stop();
                if ( el.is(':visible') ) {
                    $(el).slideUp();
                } else {
                    $(el).slideDown();
                }
            }
        },

        mounted() {

            if ( this.mapId ) {
                let data = window[`waypoint_map_locations_${this.mapId}`];
                store.commit('set_grouping_taxonomy', data.grouping_taxonomy);
            }
        },

        computed: {
            markers() {
                return store.getters.filtered_markers;
            },

            taxonomy_terms() {
                let terms = this.get_taxonomy_terms_from_locations( this.grouping_taxonomy );
                return this.sort_alphabetically( terms, 'sort_order' );
            },

            grouping_taxonomy() {
                return store.getters.grouping_taxonomy;
            },
        },
    }
</script>

<style lang="scss">
    $padding: 10px;
    .panel-container {
        border: 1px solid rgba(231, 64, 72, 0.48);
    }
    .panel-header {
        padding: $padding;
        background-color: #e74048;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }
    .panel-body {
        padding: $padding;
        display: none;
    }

    /*
    @media screen and (min-width: 768px) {
        .map-location-list-wrap {
            display: none;
        }
    }
    */
</style>