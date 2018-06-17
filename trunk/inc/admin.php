<?php

function wowprogress_action_links( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page='.WOWPROGRESS_PLUGIN_SLUG) ) .'">'.__("Configure").'</a>';
	return $links;
}
// plugin_action_links_wow-progress/wowprogress.php
add_filter( 'plugin_action_links_'.WOWPROGRESS_PLUGIN_FILE, 'wowprogress_action_links' );

function wowprogress_admin_init(){
	register_setting( WOWPROGRESS_PLUGIN_SLUG.'_plugin_options', WOWPROGRESS_PLUGIN_SLUG.'_options', 'wowprogress_validate_options' );
}
add_action('admin_init', 'wowprogress_admin_init' );


function wowprogress_add_options_page() {
	add_options_page(WOWPROGRESS_PLUGIN_NAME . " " . __('Settings', 'wowprogress'), WOWPROGRESS_PLUGIN_NAME, 'manage_options', WOWPROGRESS_PLUGIN_SLUG, 'wowprogress_render_settings_page');
}
add_action('admin_menu', 'wowprogress_add_options_page');


function wowprogress_render_settings_page() {
	?>
	<div class="wrap">
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo WOWPROGRESS_PLUGIN_NAME . " " . __('Settings', 'wowprogress') ?></h2>

		<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options';?>
		<h2 class="nav-tab-wrapper">
    		<a href="?page=wowprogress&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Settings</a>
    		<a href="?page=wowprogress&tab=raids_options" class="nav-tab <?php echo $active_tab == 'raids_options' ? 'nav-tab-active' : ''; ?>">Raids</a>
		</h2>

		<form method="post" action="options.php">
			<?php 
			if( $active_tab == 'display_options' ) {
				wowprogress_render_display_options_form();
			} else {
				wowprogress_render_display_raids_form();
			}
			?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wowprogress') ?>" />
			</p>
		</form>

		<?php	
}

function wowprogress_render_display_raids_form() {
	?>

		<?php settings_fields(WOWPROGRESS_PLUGIN_SLUG.'_plugin_options'); ?>
		<?php $options = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options'); ?>

		<!-- Table Structure Containing Form Controls -->
		<!-- Each Plugin Option Defined on a New Table Row -->
		<table class="form-table">
			<?php
                $availableRaids = wowprogress_widget::load_raids_file();
            ?>
            <tr valign="top">
                <th scope="row"><?php _e('Enabled Raids', 'wowprogress') ?></th>
                <td>
					<?php
						$exp = '';
                            foreach($availableRaids as $raid){
                            if($exp != $raid['exp']){
                                $exp = $raid['exp'];
                              	echo "<img src=" . wowprogress_widget::asset_url(wowprogress_widget::expansion_path($exp)) . "><br />";
                            }

                            $input_name = "wowprogress_options[show_raid][".$raid['tag']."]";
                    ?>

                    <input type="checkbox" name="<?php echo $input_name;?>" id="<?php echo $input_name;?>" value="1" <?php if (isset($options['show_raid'][$raid['tag']])) { checked('1', $options['show_raid'][$raid['tag']]); } ?>/>
                    <label for="<?php echo $input_name;?>"><?php echo $raid['name']?></label><br />
                	<?php } ?>
                </td>
            </tr>

        </table>
	<?php	
}


function wowprogress_render_display_options_form() {
	?>

		<?php settings_fields(WOWPROGRESS_PLUGIN_SLUG.'_plugin_options'); ?>
		<?php $options = get_option(WOWPROGRESS_PLUGIN_SLUG.'_options'); ?>

		<!-- Table Structure Containing Form Controls -->
		<!-- Each Plugin Option Defined on a New Table Row -->
		<table class="form-table">

			<tr>
				<th scope="row"><?php _e('Theme', 'wowprogress') ?></th>
				<td>
					<select name='wowprogress_options[theme]'>
						<?php foreach(wowprogress_themes() as $key => $theme){ ?>
							<option value='<?php echo $key; ?>' <?php selected($options['theme'], $key); ?>><?php echo $theme; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Show Backgrounds', 'wowprogress') ?></th>
				<td>
					<input name="wowprogress_options[show_backgrounds]" type="checkbox" value="1" <?php if (isset($options['show_backgrounds'])) { checked('1', $options['show_backgrounds']); } ?> />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Show Progress in Raid Title', 'wowprogress') ?></th>
				<td>
					<input name="wowprogress_options[show_progress_in_raid_title]" type="checkbox" value="1" <?php if (isset($options['show_progress_in_raid_title'])) { checked('1', $options['show_progress_in_raid_title']); } ?> />
				</td>
			</tr>

        </table>
	<?php	
}

function wowprogress_validate_options($input) {
	return $input;
}

?>