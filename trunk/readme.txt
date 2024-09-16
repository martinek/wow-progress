=== WoW Progress ===
Contributors: martinek
Tags: wow, warcraft, world of warcraft, guild, progress, widget, raid, boss, battle for azeroth, shadowlands, dragonflight
Requires at least: 3.0
Tested up to: 6.6.2
Stable tag: 1.21.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A widget that helps to display guild raid progress.

== Description ==

A widget that helps to display guild raid progress.
Each boss have separate option to toggle kill, heroic kill and mythic kill. Each raid can be toggled to be displayed or not and if it should be shown or collapsed by default.

There is theme support with sample Light and Dark theme. Also my own theme is attached if you are interested. You should create your own theme and customise it to match your needs.

Progress can be configured in widget menu.
Theme and backgrounds toggle is in Settings > WoW Progress menu.

If you managed to find bugs or want to correct some of my code, please don't hesitate to leave a comment or contact me on martinek@freevision.sk.

For list of raids, check latest change log. I usually add raids as people report new patch coming up on the support forums.

== Installation ==

1. Upload plugin content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In Appearance > Widget simply drag widget to sidebar.

== Frequently asked questions ==

= How do I change the look of plugin? =

You can create new plugin theme by creating new <theme>.css file in `/wp-content/themes/<your_theme>/wowprogress/themes/` folder. Then select this theme from Settings > WoW Progress.

= How do I change background images of raids? =

You can either replace image of raid in `plugins/wow-progress/images/raids/` folder, or you can upload your own and change filename in raids.json.

= I can't see new raids? =

You can enable which raids are shown in widget in Settings > WoW Progress. If you can't see your raid there, it probably isn't supported or plugin wasn't updated yet. Please let me know if any new raids are missing.

= Can I use this for other games? =

As of 1.7.0 you can! Create your own `raids.json` and put it in `/wp-content/themes/<your_theme>/wowprogress/` folder. Add your custom raid and expansion images to `/wp-content/themes/<your_theme>/wowprogress/images/` (expansions to `exp` folder, raids to `raids`). This should do it. Don't forget to activate your raids both in settings and in widget.
I have created package for FFXIV. If you are interested, let me know via email.

== Screenshots ==

1. Example of customised style for my site.
2. Sample dark theme
3. Sample light theme
4. Administration

== Changelog ==

= 1.21.0 =
* add Nerub-ar Palace raid (patch 11.0)

= 1.20.0 =
* add Amirdrassil, the Dream's Hope raid (patch 10.2)

= 1.19.0 =
* add Aberrus, the Shadowed Crucible raid (patch 10.1)

= 1.18.0 =
* add Vault of the Incarnates raid (patch 10.0)

= 1.17.0 =
* add Sepulcher of the First Ones raid (patch 9.2)

= 1.16.0 =
* add Sanctum of Domination raid (patch 9.1)

= 1.15.0 =
* add Castle Nathria raid (patch 9.0.1)

= 1.14.0 =
* add Ny'alotha, the Waking City raid (patch 8.3.0.32151)

= 1.13.0 =
* add Azshara's Eternal Palace raid (patch 8.2.0.30430)

= 1.12.0 =
* add Crucible of Storms raid (patch 8.1.0.28724)

= 1.11.0 =
* add Battle of Dazar'alor raid (patch 8.1.0)

= 1.10.1 =
* add plugin version to stylesheet and script urls
* add contributors

= 1.10.0 =
* added Uldir raid (patch 8.0.1)
* added option to show difficulty with progress in raid title
* added option to show letters instead of difficulty icons
* added option to open video links in new window

= 1.9.0 =
* added Antorus, the Burning Throne raid (patch 7.3)

= 1.8.0 =
* added Tomb of Sargeras raid (patch 7.2)

= 1.7.5 =
* fixed order of Legion raids.

= 1.7.4 =
* fixed warning if custom theme files folder does not exist on some systems.

= 1.7.3 =
* fixed order of bosses in Trial of Valor

= 1.7.2 =
* fixed styles and javascript sometimes loading via wrong protocol
* reworked paths determination for files and images. All files are now first searched for in theme directory, then in plugin directory

= 1.7.1 =
* updated wowhead tooltips js link to newer CDN to fix ssl issues

= 1.7.0 =
* added Trial of Valor raid (patch 7.1)
* added option to show raid progress in raid title
* added option to use custom raids.json and custom images from your theme folder
* updated themes, texts should now be a bit more readable
* fixed wowhead script register url to work with both http and https
* fixed a lot of PHP warnings

= 1.6.0 =
* added Emerald Nightmare raid
* added Nighthold raid
* fixed Karazhan bosses (added Nightbane)

= 1.5.2 =
* added Old raids (vanilla, tbc, wotlk)
* added World Bosses
* updated existing background images with higher resolution versions

= 1.5.1 =
* updated for WordPress 4.5
* added "Configure" link to plugin list page

= 1.5.0 =
* added Hellfire Citadel raid (patch 6.2.0)

= 1.4.1 =
* fixed problem with url file access

= 1.4.0 =
* added mythic difficulty
* added Highmaul nad Blackrock Foundry raids (patch 6.0.2)

= 1.3.0 =
* added global setting to disable raids. You might have to go to settings and enable only raids you want.
* added option to input boss kill video

= 1.2.1 =
* fixed names of bosses in Siege of Orgrimmar

= 1.2.0 =
* added Siege of Orgrimmar raid (patch 5.4)

= 1.1.0 =
* bumped up version to fix problem with incorrect version

= 1.0.2 =
* added Throne of Thunder raid (patch 5.2)

= 1.0.1 =
* added long codes for PHP in plugin Settings

= 1.0.0 =
* created plugin
