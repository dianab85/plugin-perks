<?php
/**
 * Create Admin Page
 *
 * @since 1.1.0
*/
function ed_dynam_perks_create_admin_panel() {
	add_submenu_page(
		'edit.php?post_type=dynamic-perk',
		'Admin Settings',
		'Admin Settings',
		'manage_options',
		'dynamic-perk-admin-page',
		'ed_dynam_perks_submenu_page_callback'
	);
}
add_action('admin_menu', 'ed_dynam_perks_create_admin_panel');

/**
 * Output Admin Page
 *
 * @since 1.1.0
 */
function ed_dynam_perks_submenu_page_callback() {
	?>
	<div class="wrap">
		<h2>Admin Settings</h2>
		<?php settings_errors() ?>
		<form method="post" action="options.php">
			<?php
			settings_fields('dynamic-perk-admin-page');
			do_settings_sections('dynamic-perk-admin-page');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Register Admin Options
 *
 * @since 1.1.0
*/
function ed_dynam_perks_initialize_admin_options() {
	add_settings_section('ed_dynam_perks_admin_settings_section', 'Dynamic Perk Options', 'ed_dynam_perks_admin_options_callback', 'dynamic-perk-admin-page');

	add_settings_field('ed_dynam_perks_include_plugin_css', 'Include Plugin CSS', 'ed_dynam_perks_ext_css_cb', 'dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section', array('Activate this setting to include plugin CSS.'));
	add_settings_field('ed_dynam_perks_theme', 'Theme Template', 'ed_dynam_perks_theme_cb', 'dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section');
	add_settings_field('ed_dynam_perks_template', 'ED5 perk Template', 'dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section');
	add_settings_field('dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section', array('This is the default number of perks Displayed when shortcode used'));
	add_settings_field('ed_dynam_perks_default_img_height', 'Deafult Img Height', 'ed_dynam_perks_default_img_height_cb', 'dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section', array('px'));
	add_settings_field('ed_dynam_perks_default_img_width', 'Deafult Width', 'ed_dynam_perks_default_img_width_cb', 'dynamic-perk-admin-page', 'ed_dynam_perks_admin_settings_section', array('px'));

	register_setting('dynamic-perk-admin-page', 'ed_dynam_perks_include_plugin_css');
	register_setting('dynamic-perk-admin-page', 'ed_dynam_perks_theme');
	register_setting('dynamic-perk-admin-page', 'ed_dynam_perks_template');
	register_setting('dynamic-perk-admin-page', 'ed_dynam_perks_default_img_width' );
	register_setting('dynamic-perk-admin-page', 'ed_dynam_perks_default_img_height' );
}

add_action('admin_init', 'ed_dynam_perks_initialize_admin_options');

/*
 * Settings Section Callback
 *
 * @since 1.0.1
*/
function ed_dynam_perks_admin_options_callback() {
	echo '<p>Use this shortcode to display your perks from a category, <strong>[ed-perks filter="Category Name"]</strong></p><p>You can also explicitly set the number of perks by using the number parameter(not recommended) [ed-perks filter="Category Name" number="3"]</p>';
}

/**
 * Settings Field Callback - External CSS & Js
 *
 * @since 1.1.0
*/
function ed_dynam_perks_ext_css_cb($args) {
	$external_css = checked(1, get_option('ed_dynam_perks_include_plugin_css'), false); ?>
	<input type="checkbox" id="include_ext_css" name="ed_dynam_perks_include_plugin_css" value="1" <?php echo $external_css ?>/>
	<label for="ed_dynam_perks_include_plugin_css"><?php echo $args[0] ?></label>
<?php }


/**
 * Settings Field Callback - Theme Template
 *
 *  * @since 1.1.0
 */
function ed_dynam_perks_theme_cb() {
	$version = get_option( 'ed_dynam_perks_theme' );
	?>
	<select name="ed_dynam_perks_theme" id="ed_dynam_perks_theme">
		<option value="ed5" <?php selected( $version, 'ed5' ); ?>>Ed5</option>
	</select>
	<?php
}

/**
 * Settings Field Callback - Default Img Width
 *
 * @since 1.2.0
 */
function ed_dynam_perks_default_img_width_cb($args) {
	$default_img_width = get_option('ed_dynam_perks_default_img_width');
	if(empty($default_img_width)){
		$default_img_width = '';
	}
	?>
	<input type="number" name="ed_dynam_perks_default_img_width" style="width:60px;" value='<?php echo $default_img_width; ?>' />
	<label for="ed_dynam_perks_default_img_width"> <?php echo $args[0]; ?></label>
	<?php
}

/**
 * Settings Field Callback - Default Img Height
 *
 * @since 1.2.0
 */
function ed_dynam_perks_default_img_height_cb($args) {
	$default_img_height = get_option('ed_dynam_perks_default_img_height');
	if(empty($default_img_height)){
		$default_img_height = '';
	}
	?>
	<input type="number" name="ed_dynam_perks_default_img_height" style="width:60px;" value='<?php echo $default_img_height; ?>' />
	<label for="ed_dynam_perks_default_img_height"> <?php echo $args[0]; ?></label>
	<?php
}

/**
 * Sanitize URL 
 *
 * @since 1.3.0
 */
function ed_dynam_perks_validate_url( $input ){
    //Lower case everything
    $input = strtolower($input);
    //Make alphanumeric (removes all other characters)
    $input = preg_replace("/[^a-z0-9_\s-]/", "", $input);
    //Clean up multiple dashes or whitespaces
    $input = preg_replace("/[\s-]+/", " ", $input);
    //Convert whitespaces and underscore to dash
    $input = preg_replace("/[\s_]/", "-", $input);
    return $input;
}

/**
 * Sanitize Email
 *
 * @since 1.3.0
 */
function ed_dynam_perks_validate_email( $input ){
    // Remove all illegal characters from email
    $input = str_replace(" ", "", $input);
    $input = explode(',', $input);
    $sanitized_array = array();
    foreach ($input as $key) {
    	array_push($sanitized_array, filter_var($key, FILTER_SANITIZE_EMAIL));
    }
    $input = implode(', ', $sanitized_array);
 
    return $input;
}

?>