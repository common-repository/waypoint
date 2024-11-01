# Waypoint 

Waypoint is a WordPress plugin adds easily customizable Google Maps to your 
website and allows you to manage location data.


## Requirements

* You need a valid Google Maps API Key. [Get your API Key](https://cloud.google.com/maps-platform/#get-started).

* The API Key must have **MAPS** and **PLACES** enabled.


## Getting Started

1. Install and activate Waypoint plugin
1. From WP Admin, go to Waypoint -> Settings
1. On the Google settings tab, enter your Google API Key, then save settings.

With a valid Google API key in place, we are ready to go!


## Usage

Maps are embedded onto pages with via the **[waypoint_map]** short code.

Waypoint offers two approaches to populating a map's content with locations:

### Approach 1: Location data directly in short code 

Location data can be passed directly to [waypoint_map] to generate a map for a specified location. 
This approach may be better if you do not need to manage many locations.

**[waypoint_map] Single Location Attributes**

| Key | Type | Description |
| :--- | :--- | :--- |
| lat | float | The location's geographical latitude. |
| lng | float | The location's geographical longitude. |
| address | string | The location's address. If **lat** and **lng** are specified, **address** will be ignored. Note: the specified address will be passed to the Google API in order to geocode the input address, and the *first* result returned by Google will be used. Results are cached to prevent excessive calls to Google API. |
| content | string | The content that appears in the info box when clicking a map pin, for example, the location's name. If not specified, Waypoint will attempt to generate content based on the location name and/or address if returned by Google. |

**Examples**

1. By **lat** and **lng**: ```[waypoint_map lat="33.7557301" lng="-84.392147" content="Atlanta, GA"]```
1. By **address** ```[waypoint_map address="New York City, NY"]```


### Approach 2: Configure maps in WP Admin 

**[waypoint_map] Single Location Attributes**

| Key | Type | Description |
| :--- | :--- | :--- |
| id | Post ID | The waypoint map post ID. |

*Maps* and *Locations* can added into the WP Admin area, then printed with a reference to the post ID.
This approach offers the highest level of customization.

First, you should add some *Locations* into the WordPress administration via Waypoint -> Locations.

Next, create a *Map* that will display your locations. 

Currently, a map can pull **all** locations or select locations by associated taxonomy terms, by **categories**.

Once configured, the map can be embedded via ```[waypoint_map id="123"]``` (id = post ID).

---

## Post Types

Waypoint adds two new custom post types:

1. ```waypoint_location``` (Locations)
1. ```waypoint_map``` (Maps)

### Locations

**Location Details Meta Box**

| Option | Type | Meta Key | Description |
| :--- | :--- | :--- | :--- |
| Geocode Address | string | *none*| Quickly population location details by inputting a valid address, then clicking Geocode Address. The address will be sent to Google and then geocoded. If successful, the remaining fields will populate automatically. Note, this option is not actually a stored meta value. |
| Street Address | string | ```_address_street``` | The street address 
| City | string | ```_address_city``` | The city or locality
| State | string | ```_address_state``` | The state or region
| Zip Code | string | ```_address_zip``` | The zip or postal code
| Country | string | ```_address_country``` | The country
| Latitude | float (decimal) | ```_address_lat``` | Geo-coordinates 
| Longitude | float (decimal) | ```_address_lng``` | Geo-coordinates

  
**Location Details Meta Box**

| Option | Type | Meta Key | Description |
| :--- | :--- | :--- | :--- |
| Map Pin Type | string,<br>integer | ```_map_pin_type```, <br>```_map_pin_image_id``` | Specify a custom map pin graphic for this specific location, or default.
| Map Specific Pins | JSON | ```_map_specific_pins``` | Specify a map pin graphic for this location for specific maps.


### Maps

**Map Options Meta Box - Query Locations By**

| Option | Type | Meta Key | Description |
| :--- | :--- | :--- | :--- |
| Query Locations By | string | ```_location_query_type```, <br> ```_selected_term_ids``` | Choose between showing all locations or limiting locations by taxonomy terms (Categories).

**Map Options Meta Box - Map Center**

| Option | Type | Meta Key | Description |
| :--- | :--- | :--- | :--- |
| Map Center | string | ```_map_center_type``` | Override the global map center values for this map. Note, if custom is selected, make sure to set each value.
| Desktop Center Latitude | float (decimal) | ```_map_center_desktop_lat``` | 
| Desktop Center Longitude | float (decimal) | ```_map_center_desktop_lng```
| Desktop Zoom | integer | ```_map_center_desktop_zoom```
| Mobile Center Latitude | float (decimal) | ```_map_center_mobile_lat```
| Mobile Center Longitude | float (decimal) | ```_map_center_mobile_lng```
| Mobile Center Zoom | integer | ```_map_center_mobile_zoom```

**Map Options Meta Box - Info Window Template**

| Option | Type | Meta Key | Description |
| :--- | :--- | :--- | :--- |
| Info Window Template Type | string | ```_info_window_template_type``` | Default or Custom, overriding the global info window template.
| Info Window Template | string | ```_info_window_template``` | The info window template. Allows HTML.


---

## Settings

The settings page is accessed via the WordPress admin area, Waypoint -> Settings.

Functionally specific options are grouped into different sub pages/tabs.

**Google**

| Option | Description |
| :---   | :---        |
| API Key | This is your Google Maps API key. |
| Transient Lifetime | To prevent unnecessary calls to Google API, successful geocode requests are cached in the database. The value is in seconds, or a value of 0 means "never expire". |
| Disable Transients | If disabled, geocode requests will not be cached. |
| Geocode Addresses | When checked, Waypoint will attempt to geocode addresses that are missing latitude and longitude values. This works by building an address string from the available metadata. |

 
**Maps**

The 'Maps' tab allows you to set global defaults for all maps.

| Option | Description |
| :---   | :---        |
| Map Pin Graphic | Choose between the default Google Map pin or a custom image uploaded through the media library. |
| Map Height | Pixel value, how tall maps will display on your website |
| Map Styles JSON | Map styles can be fully customized to suite your website's design with map-styling tools such as [SnazzyMaps](https://snazzymaps.com/) or [Google Maps Styling Wizard](https://mapstyle.withgoogle.com/). Copy and paste the generated JSON code to use a custom map style. Note, if invalid JSON is saved, the style will revert to default. |
| Desktop Center | Set the center latitude, longitude, and map zoom level for **desktop** viewports.
| Mobile Center | Set the center latitude, longitude, and map zoom level for **mobile** viewports.
| Info Window Template | Set the map location info window template. Accepts HTML and some predefined post variables. Note, this content is passed through [wpautop()](https://codex.wordpress.org/Function_Reference/wpautop), so line breaks are applied automatically.  


**Logs**

The 'Logs' tab shows plugin logging options.

| Option | Description |
| :---   | :---        |
| Enabled | Turns logging on or off
| Purge Logs | If checked, log files will be deleted upon saving