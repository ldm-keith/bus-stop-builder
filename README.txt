=== BUS STOP BUILDER ===
Stable tag: v0.4.1201
Repository: https://github.com/ldm-keith/bus-stop-builder
Contributors: Lentini Design and Marketing
Author link: https://lentinidesign.com/
Requires at least: 6.5
Tested up to: 6.5.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 7.0 or later

The Bus Stop Builder was created for city planners to select a bus stop location and then pick, add and scale bus stop amenities onto their creation from an amenity library that includes upgrades such as lighting, benches and shelters.

== Description ==

Technical: 
The Bus Stop Builder wordpress plugin creates a graphical tool and workflow.  Users can upload an image of a bus stop or use Google Maps and Streetview to obtain an image of a bus stop.  The user can then add and manipulate 3D graphics of amenities to the image, and save the combined result for later review.   

General:
Planners can then save their creation to show and gather support for bus stop improvements in their city. They can also use their design as a reference when moving through their city’s procurement department process.

Builder users: you can build as many designs as you want. We encourage you to think “outside the bus” and use this tool to envision what bus stops in your jurisdiction could be.

The Bus Stop Builder is only an aid, not a formal design tool. Bus stop designs should always be reviewed by a professional.

Considerations for ADA compliance, safety, amenity size and dimension should always be included before any final bus stop amenity upgrade designs are reviewed at the city level.


= DEPENDENCIES =
This plugin relies on: 
- Elementor PRO, 
- Elementor Hello theme, 
- "hello-theme-child-ldm" (custom child theme: https://github.com/ldm-keith/hello-theme-child-ldm)
The child theme has support for uploading .GLB 3d files into the Media Library, as well as assigning featured images to those GLB uploads.  The bus-stop-builder plugin depends on these items to supply "amenities" 

== Installation ==

This section describes how to install the plugin and get it working.

= Using The WordPress Dashboard =

1. This plugin will not be available on wordpress.org.  Use Uploading or FTP methods below

= Before Upgrade =

Before upgrading/updating this plugin, follow these steps:
1. Deactivate the current version of bus-stop-builder
2. Rename the old plugin folder (wp-content/plugins/bus-stop-builder_OLD) 
3. Delete the OLD version folder when the new version ahs been activated

4. Download the latest version of the plugin: https://github.com/ldm-keith/bus-stop-builder

= Upload new version in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area (NOTE: the plugin is 5M, prevented from uploading by php.ini)
3. Select `bus-stop-builder.zip` from your computer (it may have a version number in the zip file name, eg. bus-stop-builder-v1.2.zip)
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `bus-stop-builder.zip`
2. Extract the `plugin-name` directory to your computer
3. Upload the `plugin-name` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Changelog ==

= 0.10.30 =
* First Release under version control (GitHub)
* This plugin is still in BETA 

= v0.4.1201
Fix for tool not rendering in newest Firefox

