<?php
/**
 * Plugin Name: WoW Progress
 * Description: A widget that helps to display guild raid progress.
 * Author: freevision.sk
 * Version: 1.7.3
 * Author URI: http://www.freevision.sk
 * Text Domain: wowprogress
 */
/**
 * Copyright (C) 2016  Valter Martinek (email : martinek@freevision.sk)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
define( 'WOWPROGRESS_VERSION', '1.7.3' );
if ( ! defined( 'WOWPROGRESS_PLUGIN_SLUG' ) )	define( 'WOWPROGRESS_PLUGIN_SLUG',	'wowprogress');
if ( ! defined( 'WOWPROGRESS_PLUGIN_FILE' ) )	define( 'WOWPROGRESS_PLUGIN_FILE',	plugin_basename(__FILE__));
if ( ! defined( 'WOWPROGRESS_PLUGIN_NAME' ) )	define( 'WOWPROGRESS_PLUGIN_NAME',	'WoW Progress');

if ( ! defined( 'WOWPROGRESS_PLUGIN_DIR' ) )	define( 'WOWPROGRESS_PLUGIN_DIR',	plugin_dir_path( __FILE__ ));
if ( ! defined( 'WOWPROGRESS_PLUGIN_URL' ) )	define( 'WOWPROGRESS_PLUGIN_URL',	plugin_dir_url( __FILE__ ));
if ( ! defined( 'WOWPROGRESS_THEME_PLUGIN_DIR' ) )	define( 'WOWPROGRESS_THEME_PLUGIN_DIR',	get_template_directory().'/wow-progress/');
if ( ! defined( 'WOWPROGRESS_THEME_PLUGIN_URL' ) )	define( 'WOWPROGRESS_THEME_PLUGIN_URL',	get_template_directory_uri().'/wow-progress/');

if ( ! defined( 'WOWPROGRESS_THEMES_FOLDER' ) )	define( 'WOWPROGRESS_THEMES_FOLDER','themes' );
if ( ! defined( 'WOWPROGRESS_VIDEO_ICON' ) )	define( 'WOWPROGRESS_VIDEO_ICON',	'video_icon.png' );
if ( ! defined( 'WOWPROGRESS_ACHI' ) )			define( 'WOWPROGRESS_ACHI',			'<a href="//www.wowhead.com/achievement=%d?who=%s&when=%d">%s</a>' );

$nice_code = false;
if($nice_code){
	define('NL', "\n");
	define('TAB', "  ");
}
else{
	define('NL', "");
	define('TAB', "");
}

function wowp_get($arr, $key, $default = null) {
	if(isset($arr[$key])) {
		return $arr[$key];
	} else {
		return $default;
	}
}

class wowprogress_widget extends WP_Widget {

	private $WoWraids;

	function __construct() {
		// Register style sheet.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		$widget_ops = array('classname' => WOWPROGRESS_PLUGIN_SLUG, 'description' => 'WoW Progress Widget' );
		parent::__construct(false, $name = 'WoW Progress', $widget_ops);
		$this->WoWraids = $this->load_raids_file();
	}

	function register_plugin_scripts() {
		// Scripts
		wp_register_script('wowhead', '//wow.zamimg.com/widgets/power.js');
		wp_register_script(WOWPROGRESS_PLUGIN_SLUG, WOWPROGRESS_PLUGIN_URL . WOWPROGRESS_PLUGIN_SLUG . '.js');

		wp_enqueue_script('jquery');
		wp_enqueue_script('wowhead');
		wp_enqueue_script(WOWPROGRESS_PLUGIN_SLUG);

		// Styles
		wp_register_style(WOWPROGRESS_PLUGIN_SLUG, WOWPROGRESS_PLUGIN_URL . WOWPROGRESS_PLUGIN_SLUG . '.css');
		wp_enqueue_style(WOWPROGRESS_PLUGIN_SLUG);

		$options = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options');
        wp_register_style(WOWPROGRESS_PLUGIN_SLUG.'_theme', theme_file_url($options['theme']));
		wp_enqueue_style(WOWPROGRESS_PLUGIN_SLUG.'_theme');
	}

	static function expansion_path($exp) {
		return self::image_path('exp/'.$exp.'.png');
	}

	static function raid_path($raid) {
		return self::image_path('raids/'.$raid.'.png');
	}

	static function image_path($image) {
		return 'images/'.$image;
	}

	static function asset_url($path) {
		if (file_exists(WOWPROGRESS_THEME_PLUGIN_DIR . $path))
			return WOWPROGRESS_THEME_PLUGIN_URL . $path;
		else
			return WOWPROGRESS_PLUGIN_URL . $path;
	}

	static function asset_path($path) {
		if (file_exists(WOWPROGRESS_THEME_PLUGIN_DIR . $path))
			return WOWPROGRESS_THEME_PLUGIN_DIR . $path;
		else
			return WOWPROGRESS_PLUGIN_DIR. $path;
	}

    function widget($args, $instance){
		extract($args, EXTR_SKIP);
		$options = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options');
        $PROGRESS_IN_TITLE = wowp_get($options, 'show_progress_in_raid_title', false);

		echo $before_widget;
		if ( !empty( $instance['title'] ) )
			echo $before_title . $instance['title'] . $after_title;

		// Start widget
		echo NL.NL;
		echo '<div id="wowprogress">'.NL.NL;

		$exp = "";
		foreach ($this->WoWraids as $raid) {
            // Skip if raid is disabled in settings, default: hide -> 0
            if(wowp_get(wowp_get($options, 'show_raid', array()), $raid['tag'], '0') != '1') continue;
			// Skip if raid is not shown, default: hide -> false
			if(!wowp_get($instance, $raid['tag']."_show", false)) continue;

			// Output expansion header and start raid list if expansion is different from previous
			if ($exp != $raid['exp']){
				// If not first, close previous
				if ($exp != "") echo TAB.'</ul> <!-- .expansion -->'.NL.NL;

				// Set new
				$exp = $raid['exp'];

				// Output header
				echo TAB.'<div class="expansion_head"><img src="' . self::asset_url(self::expansion_path($exp)) . '" /></div>'.NL;

				// Start raids list
				echo TAB.'<ul class="expansion">'.NL;
			}

			// Start raid
			$style = ' style="background-image: url(\'' . self::asset_url(self::raid_path($raid['background'])) . '\');"';
			echo TAB.TAB.'<li class="raid"'.(wowp_get($options, 'show_backgrounds', false) ? $style : '') .'>'.NL;

			// Check if raid is complete
            $progress_count = array(
                "normal" => 0,
                "hc" => 0,
                "myth" => 0
            );
			$complete = true;
			$complete_hc = true;
            $complete_myth = true;
			foreach($raid['bosses'] as $bossid => $boss){
                if(wowp_get($instance, $raid['tag']."_".$bossid) == "on") $progress_count["normal"]++;
                else $complete = false;

                if(wowp_get($instance, $raid['tag']."_".$bossid."_hc") == "on") $progress_count["hc"]++;
                else $complete_hc  = false;

                if(wowp_get($instance, $raid['tag']."_".$bossid."_myth") == "on") $progress_count["myth"]++;
                else $complete_myth = false;
			}
            $progress = $progress_count["normal"];
            if($progress_count["hc"] > 0) $progress = $progress_count["hc"];
            if($progress_count["myth"] > 0) $progress = $progress_count["myth"];

			// Background overlay for background image lightness correction
			echo TAB.TAB.TAB.'<div class="raid_film">'.NL;
			
			// Start raid header
			echo TAB.TAB.TAB.TAB.'<div class="raid_head'.($PROGRESS_IN_TITLE ? '' : ($complete_myth ? " myth" : ($complete_hc ? " hc" : ""))).'">';

            if($PROGRESS_IN_TITLE)
                echo '<span class="raid_progress">'.$progress.'/'.count($raid['bosses']).'</span>';

			if(wowp_get($raid, 'achievement') && $complete && $instance["guild"] != "" && $instance[$raid['tag']."_time"] != "")
				printf(WOWPROGRESS_ACHI, $raid['achievement'], rawurlencode($instance["guild"]), $instance[$raid['tag']."_time"], $raid['name']);
			else
				echo $raid['name'];

			// End raid header
			echo '</div>'.NL;

			// Start boss list
			echo TAB.TAB.TAB.TAB.'<ul'.($instance[$raid['tag']."_expand"] ? "" : ' style="display: none"') . '>'.NL;

			// Output each boss
			foreach($raid['bosses'] as $bossid => $boss){
                $css_class = Array();
                $n = wowp_get($instance, $raid['tag']."_".$bossid) == "on";
                $hc = wowp_get($instance, $raid['tag']."_".$bossid."_hc") == "on";
                $myth = wowp_get($instance, $raid['tag']."_".$bossid."_myth") == "on";

                if($n || $hc || $myth){
                    $css_class[] = "down";
                }
                if($myth)
                    $css_class[] = "myth";
                elseif($hc)
                    $css_class[] = "hc";

                if(count($css_class) > 0)
                    $css_class = join(" ", $css_class);
                else
                    $css_class = false;

                echo TAB.TAB.TAB.TAB.TAB.'<li'.($css_class ? ' class="'.$css_class.'"' : '').'>';
                echo $boss;
                if(wowp_get($instance, $raid['tag']."_".$bossid."_vid") != ""){
                    echo '<a class="video_link" href="'.wowp_get($instance, $raid['tag']."_".$bossid."_vid").'">';
	                echo '<img src="'.self::asset_url(self::image_path(WOWPROGRESS_VIDEO_ICON)).'" />';
	                echo '</a>';
                }
                echo '</li>'.NL;
            }

			// End boss list
			echo TAB.TAB.TAB.TAB.'</ul>'.NL;
			
			// End raid background film
			echo TAB.TAB.TAB.'</li> <!-- .raid_film -->'.NL;

			// End raid
			echo TAB.TAB.'</li> <!-- .raid -->'.NL;
		}

		// If any exp was output, close it
		if ($exp != "")
			echo TAB.'</ul> <!-- .expansion -->'.NL.NL;

		// End widget
		echo '</div> <!-- #wowprogress -->'.NL;
		echo $after_widget;
	}

	function update($new_instance, $old_instance ){
		$instance = $old_instance;

		$instance['title']	          = strip_tags($new_instance['title']);
		$instance['guild']	          = strip_tags($new_instance['guild']);

		foreach ($this->WoWraids as $raid) {
			$instance[$raid['tag']."_time"]   = $new_instance[$raid['tag']."_time"];
			$instance[$raid['tag']."_show"]   = $new_instance[$raid['tag']."_show"];
			$instance[$raid['tag']."_expand"] = $new_instance[$raid['tag']."_expand"];

			foreach ($raid['bosses'] as $boss_id => $bossname) {
				$instance[$raid['tag']."_".$boss_id]         = $new_instance[$raid['tag'].'_'.$boss_id];
				$instance[$raid['tag']."_".$boss_id."_hc"]   = $new_instance[$raid['tag']."_".$boss_id."_hc"];
                $instance[$raid['tag']."_".$boss_id."_myth"] = $new_instance[$raid['tag']."_".$boss_id."_myth"];
                $instance[$raid['tag']."_".$boss_id."_vid"]  = $new_instance[$raid['tag'].'_'.$boss_id."_vid"];
			}
		}

		return $instance;
	}

	function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array(
			'title' => 'Progress'
			)
		);

		$this->print_form_fields($instance);
	}

	function print_form_fields($instance){
        $options = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options');

        echo '<table>';

		echo '<thead><tr><th colspan="4"></th></tr></thead>';

		echo '<tbody>';
		echo $this->form_text_input("title", __("Title:"), esc_attr($instance['title']));
		echo $this->form_text_input("guild", __("Guild", "wowprogress"), esc_attr($instance['guild']), __("Name of your guild.\nThis will be used in achievement link.", "wowprogress"));
		echo '<tr><td colspan="4"><hr /></td></tr>';
		echo '</tbody>';

		foreach ($this->WoWraids as $raid) {
            if(wowp_get($options['show_raid'], $raid['tag'], '0') != '1') continue;

			echo '<thead><tr><th colspan="4">'.$raid['name'].'</th></tr></thead>';

			echo '<tbody>';
			echo $this->form_checkbox_input($raid['tag']."_show", __("Show", "wowprogress"), wowp_get($instance, $raid['tag']."_show"));
			echo $this->form_checkbox_input($raid['tag']."_expand", __("Open", "wowprogress"), wowp_get($instance, $raid['tag']."_expand"));
			echo '</tbody>';

			echo '<thead><tr><th>N</th><th>HC</th><th>MH</th><th>Boss</th></tr></thead>';
			echo '<tbody>';

			foreach ($raid['bosses'] as $boss_id => $boss_name)
				echo $this->form_boss($raid['tag']."_".$boss_id, $boss_name, $instance);

            if(array_key_exists("achievement", $raid)) {
                echo $this->form_text_input($raid['tag']."_time", __("Time", "wowprogress"), wowp_get($instance, $raid['tag']."_time"), __("Time when guild achieved guild run achievement.\nShould be in unix micro time (ei. 1304035200000).", "wowprogress"));
            }

			echo '<tr><td colspan="4"><hr /></td></tr>';

			echo '</tbody>';
		}
		echo '</table>';
	}

	function form_checkbox($id, $state){
		return '<input type="checkbox" id="'.$this->get_field_id($id).'" name="'.$this->get_field_name($id).'"'.($state == "on" ? " checked" : "").'>&nbsp;';
	}

	function form_label($id, $label){
		return '<label for="'.$this->get_field_id($id).'">'.$label.'</label>';
	}

	function form_text($id, $value, $title = ""){
		return '<input type="text" class="widefat" id="'.$this->get_field_id($id).'" name="'.$this->get_field_name($id).'" value="'.$value.'" title="'.$title.'" />';
	}

	function form_checkbox_input($id, $label, $state){
		$res = "";
		$res .= '<tr>';
		$res .= '<td></td>';
		$res .= '<td>'.$this->form_checkbox($id, $state).'</td>';
		$res .= '<td colspan="2">'.$this->form_label($id, $label).'</td>';
		$res .= '</tr>';
		return $res;
	}

	function form_text_input($id, $label, $value, $title = ""){
		$res = "";
		$res .= '<tr>';
		$res .= '<td colspan="3">'.$this->form_label($id, $label).'</td>';
		$res .= '<td>'.$this->form_text($id, $value, $title).'</td>';
		$res .= '</tr>';
		return $res;
	}

    function form_link_input($id, $label, $value, $title = ""){
        $res = "";
        $res .= '<tr>';
        $res .= '<td>'.$this->form_label($id, $label).'</td>';
        $res .= '<td colspan="3">'.$this->form_text($id, $value, $title).'</td>';
        $res .= '</tr>';
        return $res;
    }

    function form_boss($boss_id, $boss_name, $instance){
		$boss_id_hc = $boss_id."_hc";
        $boss_id_myth = $boss_id."_myth";

		$res = "";
		$res .= '<tr>';
		$res .= '<td>'.$this->form_checkbox($boss_id, wowp_get($instance, $boss_id)).'</td>';
		$res .= '<td>'.$this->form_checkbox($boss_id_hc, wowp_get($instance, $boss_id_hc)).'</td>';
        $res .= '<td>'.$this->form_checkbox($boss_id_myth, wowp_get($instance, $boss_id_myth)).'</td>';
		$res .= '<td>'.$this->form_label($boss_id, $boss_name).'</td>';
		$res .= '</tr>';
        $res .= $this->form_link_input($boss_id.'_vid', '<img style="vertical-align: middle" src="'.self::asset_url(self::image_path('video_icon.png')).'"/>', wowp_get($instance, $boss_id."_vid"), __("URL to video.", "wowprogress"));

        return $res;
	}

	static function load_raids_file(){
        return json_decode(file_get_contents(self::asset_path('raids.json')), true);
	}
}

function wowprogress_themes(){
    $themes = array();

    $files = glob(WOWPROGRESS_PLUGIN_DIR.WOWPROGRESS_THEMES_FOLDER."/*.css");
    foreach($files as $filepath) {
        $themes['p_'. basename($filepath)] = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($filepath));
    }

    $theme_files = glob(WOWPROGRESS_THEME_PLUGIN_DIR.WOWPROGRESS_THEMES_FOLDER."/*.css");
    foreach($theme_files as $filepath) {
        $themes['t_'. basename($filepath)] = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($filepath));
    }

    return $themes;
}

function theme_file_url($key) {
    return str_replace(array(
        't_',
        'p_'
    ), array(
	    WOWPROGRESS_THEME_PLUGIN_DIR.WOWPROGRESS_THEMES_FOLDER.'/',
        WOWPROGRESS_PLUGIN_URL.WOWPROGRESS_THEMES_FOLDER.'/'
    ), $key);
}

if (!function_exists('wowprogress_widget_install')) {
    function wowprogress_widget_install() {
		$tmp = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options');
		if(!is_array($tmp)) {
			delete_option(WOWPROGRESS_PLUGIN_SLUG.'_options');
			$arr = array(
				"show_backgrounds" => "1",
				"theme" => "light.css",
                "show_progress_in_raid_title" => false,
                "show_raid" => array(
                    "soo" => "1",
                    "tot" => "1"
                )
			);
			update_option(WOWPROGRESS_PLUGIN_SLUG.'_options', $arr);
		}
    }
}
register_activation_hook(__FILE__, 'wowprogress_widget_install');


if (!function_exists('wowprogress_widget_uninstall')) {
	function wowprogress_widget_uninstall() {
		delete_option(WOWPROGRESS_PLUGIN_SLUG.'_options');
	}
}
register_uninstall_hook(__FILE__, 'wowprogress_widget_uninstall');


function wowprogress_init(){
	load_plugin_textdomain('wowprogress', false, WOWPROGRESS_PLUGIN_SLUG."/languages/");

}
add_action('plugins_loaded', 'wowprogress_init');


function wowprogress_init_widget(){
	register_widget('wowprogress_widget');
}
add_action('widgets_init', 'wowprogress_init_widget');


if (is_admin()) {
	include 'inc/admin.php';
}