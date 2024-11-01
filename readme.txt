=== Waypoint ===
Contributors: sideways8
Tags: maps, google maps, locations
Tested up to: 4.9.8
Requires PHP: 5.4
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Waypoint adds locations data and integrates Google Maps with WordPress.

== Installation ==
## Requirements

* You need a valid Google Maps API Key. [Get API Key](https://cloud.google.com/maps-platform/#get-started).

* The API Key must have **MAPS** and **PLACES** enabled.


## Getting Started

1. Install and activate Waypoint plugin
1. From WP Admin, go to Waypoint -> Settings
1. On the Google settings tab, enter your Google API Key, then save settings.

With a valid Google API key in place, we are ready to go!


## Usage

Maps are embedded onto pages with via the **[waypoint_map]** short code.

Waypoint offers two approaches to populating a map\'s content with locations:

### Approach 1: Location data directly in short code

Location data can be passed directly to [waypoint_map] to generate a map for a specified location.
This approach may be better if you do not need to manage many locations.

**[waypoint_map] Single Location Attributes**

| Key | Type | Description |
| :--- | :--- | :--- |
| lat | float | The location\'s geographical latitude. |
| lng | float | The location\'s geographical longitude. |
| address | string | The location\'s address. If **lat** and **lng** are specified, **address** will be ignored. Note: the specified address will be passed to the Google API in order to geocode the input address, and the *first* result returned by Google will be used. Results are cached to prevent excessive calls to Google API. |
| content | string | The content that appears in the info box when clicking a map pin, for example, the location\'s name. If not specified, Waypoint will attempt to generate content based on the location name and/or address if returned by Google. |

**Examples**

1. By **lat** and **lng**: ```[waypoint_map lat=\"33.7557301\" lng=\"-84.392147\" content=\"Atlanta, GA\"]```
1. By **address** ```[waypoint_map address=\"New York City, NY\"]```


### Approach 2: Configure maps in WP Admin

*Maps* and *Locations* can added into the WP Admin area, then printed with a reference to the post ID.
This approach offers the highest level of customization.

First, you should add some *Locations* into the WordPress administration via Waypoint -> Locations.

Next, create a *Map* that will display your locations.

Currently, a map can pull **all** locations or select locations by associated taxonomy terms, by **categories**.

Once configured, the map can be embedded via ```[waypoint_map id=\"123\""]``` (where 123 is the post ID).