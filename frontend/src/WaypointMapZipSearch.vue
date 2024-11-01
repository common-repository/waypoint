<template>
    <div class="zip-search">

        <div class="row">

            <div class="col-sm-12 col-md-6 col-lg-5">
                <label>Zip Code</label>
                <input type="text" class="form-control" v-model="values.zip_code" :placeholder="default_zip">
            </div>

            <div class="col-sm-12 col-md-6 col-lg-5">
                <label>Within</label>
                <select class="form-control" v-model="values.within">
                    <option value="">Select distance...</option>
                    <option v-for="option in options.within" :value="option.value">{{ option.label }}</option>
                </select>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-2">

                <div class="hidden-xs hidden-sm hidden-md">
                    <div class="pull-right">
                        <label>&nbsp;</label><br>
                        <div class="btn-group">
                            <button class="btn btn-default reset" @click="clear" :disabled="busy">Clear</button>
                            <button class="btn btn-primary search" @click="apply_filter" :disabled="busy">Search
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row hidden-lg">
                    <div class="col-xs-6">
                        <label>&nbsp;</label><br>
                        <button class="btn btn-default reset btn-block" @click="clear" :disabled="busy">Clear</button>
                    </div>
                    <div class="col-xs-6">
                        <label>&nbsp;</label><br>
                        <button class="btn btn-primary search btn-block" @click="apply_filter" :disabled="busy">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <p style="padding-top: 10px;">Showing of {{ total_filtered_markers }} of {{ total_markers }} locations.</p>
    </div>
</template>

<script>
    import store from './store';

    export default {

        name: 'waypoint-map-zip-search',

        computed: {
            total_markers() {
                return store.getters.markers.length;
            },

            total_filtered_markers() {
                return store.getters.filtered_markers.length;
            },

            default_zip() {
                return window._waypoint_zip_search.default_zip;
            }
        },

        data() {
            return {
                busy: false,
                values: {
                    zip_code: '',
                    within: ''
                },
                options: {
                    within: [
                        {value: 5, label: '5 Miles'},
                        {value: 10, label: '10 Miles'},
                        {value: 25, label: '25 Miles'},
                        {value: 50, label: '50 Miles'},
                        {value: 100, label: '100 Miles'},
                    ]
                }
            }
        },

        methods: {

            clear() {
                store.commit('set_filtered_ids', []);
            },

            apply_filter() {
                let l = this.get_locations();
            },

            get_locations() {

                let zip = this.values.zip_code.trim(),
                    distance = this.values.within;

                if ( '' === zip ) {
                    alert('Please input a zip code.');
                    return;
                }

                if ( '' === distance ) {
                    alert('Please select a distance.');
                    return;
                }

                this.busy = true;

                jQuery.post({
                    method: 'post',
                    url: window._waypoint_zip_search.ajax_url,
                    data: {
                        zip_code: zip,
                        within: distance
                    }
                })
                .done( r => {

                    // Miles -> Zoom
                    let zooms = {
                        5: 11,
                        10: 10,
                        25: 9,
                        50: 8,
                        100: 7
                    };

                    let ids = [];
                    _.each(r, el => {
                        ids.push(el.ID);
                    });
                    if ( 0 === ids.length ) {
                        alert(`No locations found within ${distance} miles of ${zip}.`)
                    } else {
                        let lat = Number( r[0].lat) ,
                            lng = Number( r[0].lng ),
                            coords = {lat: lat, lng: lng},
                            zoom = zooms[distance];
                        store.commit('set_map_center', coords);
                        store.commit('set_zoom', zoom);
                    }
                    store.commit('set_filtered_ids', ids);
                })
                .fail( r => {
                    let response = r.responseJSON;
                    alert(response);
                })
                .always( r => {
                    this.busy = false;
                });
            }
        }
    }
</script>