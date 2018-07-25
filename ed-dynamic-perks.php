<?php
/**
 * Plugin Name: Edealer Dynamic Perks
 * Description: Create Dynamic Call To Action for Website Users
 * Version: 1.5
 * Author: eDealer WebDev
 * Text Domain: ed_dynam_perks
 * License: GPL2
 */

//Admin Options
require_once(plugin_dir_path(__FILE__) . 'admin/admin-base.php');

/**
 * init custom post type "Dynamic perks"
 */
function ed_dynam_perks_init() {
	$labels = array(
		'name'				 	=> __('Dynamic Perks'),
		'singular_name'		 	=> __('Dynamic Perk'),
		'add_new'			 	=> __('Add New'),
		'add_new_item'		 	=> __('Add New Dynamic Perk'),
		'edit_item'			    => __('Edit Dynamic Perk'),
		'new_item'			    => __('New Dynamic Perk'),
		'view_item'			 	=> __('View Dynamic Perk'),
		'search_items'		 	=> __('Search Dynamic Perk'),
		'not_found'			    => __('No Dynamic Perks found.'),
		'not_found_in_trash'    => __('No Dynamic Perks found in trash.')
	);

	register_post_type('dynamic-perk', array(
		'labels'		    => $labels,
		'supports'		    => array('sortable'),
		'menu_position' 	=> 5,
		'menu_icon'         => 'dashicons-share-alt2',
		//'taxonomies'		=> array('post_tag', 'category'),
		'description'		=> __('Specific Dynamic Perk Pages'),
		'public'			=> true,
		'has_archive'       => false,
	));
}
add_action('init', 'ed_dynam_perks_init');

/**
 * init admin facing scripts & styles
 */
function ed_dynam_perks_admin_script_style($hook) {
	global $post_type;

	if($post_type == 'dynamic-perk'){
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'ed-dynam-perks-icon-file', 'http://websites.edealer.ca/assets/icons/ed-icons/style.css', false, '10', 'all' );
		wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'ed-dynam-perks-admin-css', plugins_url( '', __FILE__) . '/admin/css/admin.css' );
		wp_register_script( 'ed-dynam-perks-admin', plugins_url( '', __FILE__) . '/admin/js/admin.js?vers1.1',array( 'jquery-ui-sortable' ),'1', true );
		wp_register_script( 'ed-dynam-perks-media-uploader', plugins_url( '', __FILE__) . '/admin/js/media-uploader.js' );
		wp_enqueue_script( 'ed-dynam-perks-admin' );
		wp_enqueue_script( 'ed-dynam-perks-media-uploader' );
	}
}
add_action('admin_enqueue_scripts', 'ed_dynam_perks_admin_script_style');

/**
 * register frontend styles & scripts
 */
function ed_dynam_perks_scripts_style($hook) {
	wp_register_style( 'ed-dynam-perks-css', plugins_url( '', __FILE__) . '/library/css/dynam-perks-style.css?vers1.7', array(), '1.0.1', all );
	wp_register_script( 'ed-dynam-perks-scripts', plugins_url( '', __FILE__) . '/library/js/dynam-perks-scripts.js?vers1.2', array(), '', true );

	if(get_post_type() == 'dynamic-perks'){
		wp_enqueue_style( 'ed-dynam-perks-css' );
		wp_enqueue_script( 'ed-dynam-perks-scripts' );
	}
}
//add_action('wp_enqueue_scripts', 'ed_dynam_perks_scripts_style');


/**
 * init metaboxes
 */
function ed_dynam_perks_info_metabox() {
	add_meta_box(
		'ed-dynam-perk-details-metabox',
		__('perk Details', 'dynamic-perk'),
		'ed_dynam_perks_show_custom_meta_box',
		'dynamic-perk',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'ed_dynam_perks_info_metabox');

/**
 * Metabox Callback
 *
 */
function ed_dynam_perks_show_custom_meta_box() {
	$input_groups = array("general");
	global $post;
	echo '<div class="ed-admin-mb">';
	echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
	ed_dynam_perks_create_tabs($input_groups);
	echo '</div>';
}

/**
 * Metabox Callback
 */
function ed_dynam_perks_create_tabs($input_groups){
	global $post;
	$nav_tab_class = 'nav-tab-wrapper';

	echo "<div class='" . $nav_tab_class . "'>";
	foreach ($input_groups as $key => $value) {
		if(file_exists(realpath(dirname(__FILE__)) . '/admin/input-groups/' . $value . '.php')){
			$title = ucwords(str_replace("-"," ",$value));
			$active = ($key == 0) ? "active" : "";
			echo "<a class='nav-tab " . $active . " " . strtolower($title) . "' data-tab='" . $key . "'>" . $title . "</a>";
		}
	} 
	echo "</div>";

	foreach ($input_groups as $key => $value) {
		if(file_exists(realpath(dirname(__FILE__)) . '/admin/input-groups/' . $value . '.php')){
			$active = ($key == 0) ? "active" : "";
			echo "<div class='group " . $active . " " . $value . "' data-content='" . $key . "'>";
				include('admin/input-groups/' . $value . '.php');
			echo "</div>";
		}
	} 
}

/**
 * SAVE the data
 * @param $post_id
 *
 * @return mixed
 */
function ed_dynam_perks_save_custom_meta($post_id) {

	// verify nonce
	if(isset($_POST['custom_meta_box_nonce'])){
		if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__))) return $post_id;
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		// check permissions
		if ('page' == $_POST['dynamic-perks']) {
			if (!current_user_can('edit_page', $post_id))
				return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		// loop through fields and save the data for properties & links (not applicable for checkboxes)
		
		$field_id = array('dynam_perk_img_path','dynam_perk_img_seo','dynam_perk_title_text','dynam_perk_description_text','dynam_perk_rewards_program');		
		//$field_id = array('dynam_perk_img_path','dynam_perk_img_seo','dynam_perk_title_text','dynam_perk_description_text','dynam_perk_link_int','dynam_perk_date_start','dynam_perk_date_end');
		foreach ( $field_id as $field){
			$old = get_post_meta( $post_id, $field, true );
			$new = $_POST[ $field ];

			if(is_array($new)){
				$new = array_filter($new);
				$new = array_values($new);
			}
			if ( $new && $new != $old ) {
				
				update_post_meta( $post_id, $field, $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, $field, $old );
			}
		}
	}
}
add_action('save_post', 'ed_dynam_perks_save_custom_meta');

/**
 * Create Custom Categories
 *
 */
function ed_dynam_perks_categories() {
	$labels = array(
		'name'					=> __('Perk Category', 'taxonomy general name'),
		'singular_name'	=> __('Perk Category', 'taxonomy singular name'),
		'search_items'	=> __('Search Perk Categories'),
		'menu_name'			=> __('Perk Categories')
	);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array('slug' => 'dynamic-perk-category'),
	);
	register_taxonomy('dynamic-perk-category', array('dynamic-perk'), $args);

	ed_dynam_perks_create_default_cats();
}
add_action('init', 'ed_dynam_perks_categories');

/**
 * Create Default perk Categories
 *
 */
function ed_dynam_perks_create_default_cats(){
	$parent_term = term_exists( 'dynamic-perk-category' ); // array is returned if taxonomy is given
	$parent_term_id = $parent_term['term_id']; // get numeric term id
	$version = get_option( 'ed_dynam_perks_theme' );
	if($version == 'ed5'){
		$default_terms = array('Home Row 1', 'Home Row 2');
	} else {
		$default_terms = array('Home');
	}

	foreach ($default_terms as $term) {
		$term_slug = str_replace(' ', '-', strtolower($term));
		$term_descrip = $term . ' Perk Category';
		wp_insert_term(
		  $term, // the term 
		  'dynamic-perk-category', // the taxonomy
		  array(
		    'description'=> $term_descrip,
		    'slug' => $term_slug,
		    'parent'=> $parent_term_id
		  )
		);
	}
}

/**
 * Shortcode To Output perks
 *
 */
function ed_dynam_perks_shortcode($atts){
	ob_start();

	//grab shortcode filters set by user
	$a = shortcode_atts( array(
		'filter' => "",
		'number' => "",
		'template' =>""
	), $atts );

	if(empty($a['filter'])){
		//display all categories if filter shortcode parameter not used
		$dynam_perks_categories = get_terms('dynamic-perk-category');
		$dynam_perks_categories_array = array();
	
		foreach ($dynam_perks_categories as $category) {
			array_push($dynam_perks_categories_array, $category->slug);
		}
		$filter_categories = $dynam_perks_categories_array;
	} else{
		//strip spaces & convert to lowercase
		$filter = str_replace(',  ', ',', strtolower($a['filter']));
		$filter = str_replace(', ', ',', $filter);
		$filter = str_replace(' ', '-', $filter);
		$dynam_perks_filter = explode(",", $filter);

		//remove non-existing filters
		$filter_categories = filter_dynam_perks_categories($dynam_perks_filter);
	}

	if(empty($a['number'])){
		$shortcode_display_num = "";
	} else {
		$shortcode_display_num = $a['number'];
	}

	$dynamic_perks = wp_query_dynamic_perks($filter_categories, $shortcode_display_num);

	//call the appropriate template - (list is default)
	if($dynamic_perks->post_count > 0){
		static $perk_count=1;
		if($perk_count > 1){
			$perk_count_class = 'secondary';
		} else {
			$perk_count_class = '';
		}
		
		$theme = get_option('ed_dynam_perks_theme');
		if(empty($theme)){
			$theme = 'ed5';
		}

		$template = $a['template'];
		if(empty($template)){
			$template = '1';
		}

		include( 'templates/perks/' . $theme . '/option-'. $template . '/perk-base.php' );
		$perk_count++;
	}

	return ob_get_clean();
}
add_shortcode('ed-perks', 'ed_dynam_perks_shortcode');

/**
 * Filter Actual Dynamic perk Categories
 * returns array
 */
function filter_dynam_perks_categories($dynam_perks_filter){
	$filter_categories = $dynam_perks_filter;
	$dynam_perks_categories = get_terms('dynamic-perk-category');
	$dynam_perks_category_array = array();
	$key = 0;
	//TODO: if no filter is applied, get all category ids and render all categories

	if ($filter_categories !== NULL || $filter_categories !== 0 || $filter_categories !== ""){
		//create an array of all Dynamic perk categories
		foreach ($dynam_perks_categories as $category) {
			array_push($dynam_perks_category_array, $category->slug);
		}

		//compare Dynamic perk category array with shortcode filters
		foreach ($filter_categories as $filter_category) {
			//if false remove from dynam_perks_filter
			if(!in_array($filter_category, $dynam_perks_category_array)){
				echo '<script type="text/javascript">console.log("' . $filter_category . ' Dynamic perk category does not exist");</script>';
				unset($filter_categories[$key]);
			}
			$key++;
		}
	} else {
		echo "no filter categories</br>";
	}

	return $filter_categories;
}

/**
 * WP Query for Dynamic perks
 *
 */
function wp_query_dynamic_perks($filter_categories, $shortcode_display_num){
	if(empty($shortcode_display_num)){
		$perks_display_num = get_option('ed_dynam_perks_display_num');
		if(empty($perks_display_num) || !isset($perks_display_num)){
			$version = get_option( 'ed_dynam_perks_theme' );
			if($version == 'ed5'){
				$perks_display_num = '3';
			} else {
				$perks_display_num = '3';
			}
		}
	} else {
		$perks_display_num = $shortcode_display_num;
	}

	$args = array(
		'post_type'      => 'dynamic-perk',
		'posts_per_page' => -1,
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'meta_query'    => array(
			'relation' => 'OR',
			array(
				'relation' => 'AND',

				array(
					'key'		=> 'dynam_perk_date_end',
					'value' 	=> date('Y/m/d'),
					'compare' 	=> '>=',
				),

				array(
					'key'		=> 'dynam_perk_date_start',
					'value' 	=> date('Y/m/d'),
					'compare' 	=> '<=',
				),
			),
			array(
				array(
					'key'     => 'dynam_perk_date',
					'compare' => 'NOT EXISTS'
				),
			)
		),
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'dynamic-perk-category',
				'field'			=> 'slug', 
				'terms'			=> $filter_categories
			)	
		)
	);

	return $dynamic_perks = new WP_Query( $args );
}

/**
 * Overide Title Using perk Title
 *
 */
function mod_post_title( $data ){
  if($data['post_type'] == 'dynamic-perk') {
	  if(isset($_POST['dynam_perk_title_text'])) {
		$data['post_title'] =  $_POST['dynam_perk_title_text'];
	  }
  }
  return $data; // Returns the modified data.
}
add_filter( 'wp_insert_post_data' , 'mod_post_title' , '99', 1 ); // Grabs the inserted post data so you can modify it.

/**
 * overview column headers
 *
 */
function ed_dynam_perks_columns($defaults) {
	//unset($defaults['date']);
	unset( $defaults['wpseo-score'] );

	$defaults['dynam_perks_status'] = __( 'perk Display Status', 'dynamic-perk' );
	$defaults['dynam_perks_img'] = __( 'Perk Img', 'dynamic-perk' );

	return $defaults;
}
add_filter('manage_edit-dynamic-perk_columns', 'ed_dynam_perks_columns');

/**
 * custom column headers
 *
 */
function ed_dynam_perks_expire_column($column_name, $post_id) {
	$perks_display_num = get_option('ed_dynam_perks_display_num');
	if(empty($perks_display_num) || !isset($perks_display_num)){
		$version = get_option( 'ed_dynam_perks_theme' );
		if($version == 'ed5'){
			$perks_display_num = '3';
		} else {
			$perks_display_num = '3';
		}
	}

	$dynam_perk_status = get_post_status( $post_id );
	$dynam_perk_date = get_post_meta( get_the_id(), 'dynam_perk_date', true);
	$dynam_perk_date_end = get_post_meta( get_the_id(), 'dynam_perk_date_end', true);
	$curr_date = date('Y/m/d');

	$terms = get_terms( 'dynamic-perk-category', array(
	    'hide_empty' => false,
	) );

	global $active_counter;
	global $counter;
	global $prev_category;

	if('dynam_perks_img' == $column_name) {
		$img = get_post_meta( get_the_id(), 'dynam_perk_img_path', true);		
		if(strpos($img, 'http') !== false){
			echo '<img src="' . $img . '" style="width:150px; height:auto;"/>';
		} elseif(!empty($img)){
			echo '<img src="//'. $_SERVER['HTTP_HOST'] . $img . '" style="width:150px; height:auto;"/>';
		}
	}
}
add_action('manage_dynamic-perk_posts_custom_column', 'ed_dynam_perks_expire_column', 10, 2);

/**
 * Saving Sort Order
 *
 */
function ed_dynam_perks_sort_posts(){	

		if( empty($_POST['action'])){return;}

		$data = array_map('sanitize_text_field',$_POST['sort']);
		$messages = array();

		foreach($data as $k => $v)
		{
			$id = ltrim($v, 'post-'); //Trim the "post-" prefix from the id
			$index = ($k + 1); //Make sure our sorting index starts at #1
		
			$my_post = array(
			    'ID'           => $id,
			    'menu_order'   => $index,
			);

			wp_update_post( $my_post );

		}
		
		exit();
}
add_action('wp_ajax_sort-posts', 'ed_dynam_perks_sort_posts');

/**
 * Displaying the sort order on Admin
 *
 */
function ed_dynam_perks_sort_orderby( $query ) {
	global $post_type;

	if($post_type == 'dynamic-perk'){
		global $pagenow;

	    if( !is_admin() ){ return; } //If we're not in the backend, quit
	    
	    if( isset( $_GET['post_type'] ) ) //Make sure post type is set
	    {	    
	    	//Make sure we're on the All Post Screen
			if( 'edit.php' === $pagenow && 'dynamic-perk' === $_GET['post_type'] ){
		        $query->set('orderby','menu_order');
		        $query->set('order', 'ASC');
			}	    
	    }
	}
}
add_action( 'pre_get_posts', 'ed_dynam_perks_sort_orderby' );