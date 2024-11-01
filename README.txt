=== wp-IniFileLoad ===
Contributors: Masarki Kondo
Donate link: http://www.belive.jp/archives/wp-inifileload-1_0/
Tags: inifile common data
Requires at least: 2.1
Tested up to: 2.0
Stable tag: 1.1.1

wp-IniFileLoad is a simple plug-in. This plug in offers functions to load necessary common data from IniFile in your WordPress.

== Description ==

 wp-IniFileLoad is a simple plug-in.
 This plug in offers functions to load necessary common data from IniFile in your WordPress.
 The result is returned an associative array for.

== Installation ==

1. Download wp-IniFileLoad-1.1.1.zip 
2. Unpack/upload the "wp-IniFileLoad.php" into "wp-content/plugins/". 
3. Activate the plugin from the Admin interface; "Plugins". 
4. Upload your IniFile in your theme or "wp-content/themes/common/".
5. You load it as arrangement data in function "wp_load_inifile(the-inifile-name)".

== IniFile Format ==

1. The structure of the ini file is the same as the php.ini's.
2. The result is returned an associative array for.(eg. sample01.ini)
3. Section "data" and "function" are defined as a special section.(eg. sample03.ini)
4. Section "data" is put in a "data" key.(eg. sample01.ini)
5. You can put a space between two names in a section name (exclude "function"). When you wrote the section name, it is parsed to a double associative arrays.(eg. sample02.ini)
6. When there is a "function" section, with a lambda function defined with a value, the result is converted.(eg. sample03.ini)
7. The section except "data" and "function" is put in an "option" key.(eg. sample01.ini)

== History ==

= ver. 1.0 =
 2008.02.23
 first version.
= ver. 1.1 =
 2008.03.12
 INI data came to have cache. And I added function wp_clear_inidata and wp_reload_inifile.
= ver. 1.1.1 =
 2008.04.11
 I did not limit a double associative array to a "data" section.
