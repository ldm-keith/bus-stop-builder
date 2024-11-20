<?php

/**
 *
 * @package     Bus Stop Builder
 * @author      Lentini Design and Marketing
 * @copyright   2024 Lentini Design and Marketing
 * @license     
 *
 * @wordpress-plugin
 * Plugin Name: Bus Stop Builder
 * Plugin URI:
 * Description: A visual tool for designing and enhancing existing bus stops - shortcode [bus-stop-builder]
 * Version:     0.11.4
 * Author:      Lentini Design and Marketing
 * Author URI:  https://lentinidesign.com
 * Text Domain: bus-stop-builder
 * License:     
 * License URI: 
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );


	

// Plugin Defines
define( "BSB_FILE", __FILE__ );
define( "BSB_DIRECTORY", dirname(__FILE__) );
define( "BSB_TEXT_DOMAIN", dirname(__FILE__) );
define( "BSB_DIRECTORY_BASENAME", plugin_basename( BSB_FILE ) );
define( "BSB_DIRECTORY_PATH", plugin_dir_path( BSB_FILE ) );
define( "BSB_DIRECTORY_URL", plugins_url( null, BSB_FILE ) );


/** Need api key from DB to include in enque scripts */ 
$bsb_api_key_query = "
SELECT post_content FROM wp_posts where post_type = 'bsb_info_boxes' 
and post_status = 'publish' 
and post_name = 'bsb_google_api_key';
";
function bsb_get_apikey($results){
 global $bsb_api_key_query;
  $output = "";
  foreach( $results as $key => $row) {
    $output= $row->post_content;
  }
  return $output;
}
global $wpdb;
$results = $wpdb->get_results( $bsb_api_key_query );
define( "BSB_GOOGLE_APIKEY", bsb_get_apikey($results) );
    
    /**
     * Enqueue the main Plugin user scripts and styles
     * @method plugin_enqueue_scripts
     */
    function bsb_enqueue_scripts() {
     	 
        
		wp_register_script( 'bsb-google-maps', "https://maps.googleapis.com/maps/api/js?key=" . BSB_GOOGLE_APIKEY . "&v=weekly&libraries=places&loading=async", array(), null );
        
		
		wp_register_style( 'bsb-user-style', BSB_DIRECTORY_URL . '/assets/index.css', array(), '1.01' );
        
		wp_enqueue_script('bsb-google-maps');
		wp_enqueue_style('bsb-bs-css','https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css', array(), null);
		
		//NOTE: this script is loaded in the footer.  ALso isn't loaded until jQuuery is loaded
		wp_enqueue_script( 'bsb-bs-js', "https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js", array('jquery'), null, true );
        wp_enqueue_script_module('bsb-user-script-module', BSB_DIRECTORY_URL . '/assets/index.js', array(), '1.56' );
		wp_enqueue_script('bsb-custom-script-module', BSB_DIRECTORY_URL . '/assets/custom.js', array(), '0.88' );
		wp_enqueue_script('bsb-amenities', home_url() ."/wp-json/".plugin_basename(BSB_DIRECTORY). '/v1/amenities', array(), '0.2' );
        wp_enqueue_script( 'ajax-script', BSB_DIRECTORY_URL. '/js/bsb_ajax.js', array('jquery') );
		wp_enqueue_style('bsb-user-style');
		
		// this is output on rest /amenities JS page
		//wp_add_inline_script('bsb-amenities', 
		//"bsb_wp_content_url = \"" .WP_CONTENT_URL. "/uploads/\"; 
		//console.log('bsb_wp_content_url='+bsb_wp_content_url);
		//"
		//, 'before');	
    }
    add_action( 'wp_enqueue_scripts', 'bsb_enqueue_scripts' );
    
    function bus_stop_builder_shortcode($atts) {
		
		$popup_header_post = get_posts(array(
        'post_type' => 'bsb_info_boxes',
        'name' => 'bsb_popup_header', // Slug of the post
        'posts_per_page' => 1
    ));
		 if (!empty($popup_header_post) && isset($popup_header_post[0]->post_content)) {
        $popup_header = apply_filters('the_content', $popup_header_post[0]->post_content); // Use apply_filters to render the content
    }
		$popup_thanksheader_post = get_posts(array(
        'post_type' => 'bsb_info_boxes',
        'name' => 'bsb_popup_thanks_header', // Slug of the post
        'posts_per_page' => 1
    ));
		 if (!empty($popup_thanksheader_post) && isset($popup_thanksheader_post[0]->post_content)) {
        $popup_thanks_header = apply_filters('the_content', $popup_thanksheader_post[0]->post_content); // Use apply_filters to render the content
    }
		
	  $Content = '<div id="root"></div><div id="formModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true"> <div class="modal-dialog modal-dialog-centered" role="document"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title" id="formModalLabel">'.$popup_header.'</h5><h5 class="modal-title" id="formModalThanksLabel">'.$popup_thanks_header.'</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> </div> <div class="modal-body"> ' . do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]') . ' </div> </div> </div> </div>'; 
      return $Content;
    }
    add_shortcode('bus-stop-builder', 'bus_stop_builder_shortcode');
	
	/**
	* NOTES: For multi page user workflows
	*/
	// https://wordpress.stackexchange.com/questions/268315/multi-step-form-custom-plugin
	


// Select info for 3D Amenitiy media, for output into a JSON object via WP REST route below
// v 0.1
$bsb_amenity_query_v11 = "
SELECT DISTINCT
p.ID AS ID, 
REPLACE(t.Name, '\\\"', '\\\\\\\"') AS category,  
REPLACE(p.post_title, '\\\"', '\\\\\\\"') AS title,
REPLACE(p.post_content, '\\\"', '\\\\\\\"')  AS description,
pm.meta_value AS model_url, 
pmt.meta_value AS thumb_url,
CONCAT('bsb_wp_content_url+\"',pm.meta_value) AS model_url_full,
CONCAT('bsb_wp_content_url+\"',pmt.meta_value) AS thumb_url_full

FROM wp_posts p
INNER JOIN wp_term_relationships r ON r.object_id = p.ID
INNER JOIN wp_terms t ON r.term_taxonomy_id = t.term_id
INNER JOIN wp_term_taxonomy tx ON tx.term_id = t.term_id AND tx.parent IN (select term_id from wp_terms where slug = 'amenities') 
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
INNER JOIN wp_postmeta pmt ON pmt.meta_key = '_wp_attached_file' AND pmt.post_id IN (select meta_value from wp_postmeta where post_id = p.ID and meta_key = 'thumb_id' )
where p.post_type = 'attachment'
AND p.post_mime_type = 'model/glb-binary'
AND p.post_status <> 'trash'
order by category, title, description;
";	

//v 0.2 

$bsb_amenity_query = "
SELECT DISTINCT
p.ID AS ID, 
REPLACE(t.Name, '\\\"', '\\\\\\\"') AS category,  
REPLACE(p.post_title, '\\\"', '\\\\\\\"') AS title,
REPLACE(p.post_content, '\\\"', '\\\\\\\"')  AS description,
pm.meta_value AS model_url, 
pmt.meta_value AS thumb_url,
pms.meta_value AS size_factor

FROM wp_posts p
INNER JOIN wp_term_relationships r ON r.object_id = p.ID
INNER JOIN wp_terms t ON r.term_taxonomy_id = t.term_id
INNER JOIN wp_term_taxonomy tx ON tx.term_id = t.term_id AND tx.parent IN (select term_id from wp_terms where slug = 'amenities')
INNER JOIN wp_postmeta pms ON p.ID = pms.post_id AND pms.meta_key = 'size_factor'
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
INNER JOIN wp_postmeta pmt ON pmt.meta_key = '_wp_attached_file' AND pmt.post_id IN (select meta_value from wp_postmeta where post_id = p.ID and meta_key = '_thumbnail_id' )
where p.post_type = 'attachment'
AND p.post_mime_type = 'model/glb-binary'
AND p.post_status <> 'trash'
order by category, title, description;
";

// v 0.2
/*
SELECT DISTINCT
p.ID AS ID, 
REPLACE(t.Name, '\"', '\\\"') AS category,  
REPLACE(p.post_title, '\"', '\\\"') AS title,
REPLACE(p.post_content, '\"', '\\\"')  AS description,
pm.meta_value AS model_url, 
pmt.meta_value AS thumb_url,
pms.meta_value AS size_factor,
CONCAT('bsb_wp_content_url+"',pm.meta_value) AS model_url_full,
CONCAT('bsb_wp_content_url+"',pmt.meta_value) AS thumb_url_full

FROM wp_posts p
INNER JOIN wp_term_relationships r ON r.object_id = p.ID
INNER JOIN wp_terms t ON r.term_taxonomy_id = t.term_id
INNER JOIN wp_term_taxonomy tx ON tx.term_id = t.term_id AND tx.parent IN (select term_id from wp_terms where slug = 'amenities')
INNER JOIN wp_postmeta pms ON p.ID = pms.post_id AND pms.meta_key = 'size_factor'
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
INNER JOIN wp_postmeta pmt ON pmt.meta_key = '_wp_attached_file' AND pmt.post_id IN (select meta_value from wp_postmeta where post_id = p.ID and meta_key = '_thumbnail_id' )
where p.post_type = 'attachment'
AND p.post_mime_type = 'model/glb-binary'
AND p.post_status <> 'trash'
order by category, title, description;
*/

//Select media Csategory meta info, like cat descriptions
$bsb_amenity_cat_desc_query = "SELECT REPLACE(t.Name, '\\\"', '\\\\\\\"') AS category, REPLACE(tx.description, '\\\"', '\\\\\\\"') AS description, t.slug AS cat_slug, t.term_id AS cat_id, tx.parent AS parent_id FROM wp_term_taxonomy tx inner join wp_terms t ON t.term_id = tx.term_id AND tx.parent IN (select term_id from wp_terms where slug = 'amenities');";

$bsb_info_boxes_query = "
SELECT post_name, post_content FROM wp_posts where post_type = 'bsb_info_boxes' and post_status = 'publish';
";


/**
 * [LDM] Add WP REST routes.  Used to return dynamic Javascript to the client
 *
 */
	add_action( 'rest_api_init', function () {
	
	    // https://bsh-ldm-build.com/visualizer/wp-json/bus-stop-builder/v1/amenities
		register_rest_route( 'bus-stop-builder/v1', '/amenities', array(
			'methods' => 'GET',
			'permission_callback' => function(){ return true;}, //ok for public use
			'callback' => 
		        function ( WP_REST_Request $request ) {
                  header('Access-Control-Allow-Origin: *');
				  header('Content-Type: application/javascript');
				  //amenities rest route should query WPDB and return JSON object
			      global $wpdb;
				  global $bsb_amenity_query;
				  global $bsb_amenity_cat_desc_query;
				  global $bsb_info_boxes_query;
				  
				  //Output Full server URL path to uploads folder in JS variable - Other scripts only store relative paths to BSB Media 
	              echo "bsb_wp_content_url = \"" .WP_CONTENT_URL. "/uploads/\";\n\n";
				  echo "bsb_plugin_url = \"" .BSB_DIRECTORY_URL. "/\";\n\n";
				  echo "bsb_home_url = \"" .home_url(). "/\";\n\n";			  
				  echo "var bsb_tool_path = jQuery(location).attr('href');";
				  
				  //Output list of Amenity cats and model items in JS variable
				  $results = $wpdb->get_results( $bsb_amenity_query );
				  echo bsb_get_amenity_reults($results);
				  
				  //Output another JS variable with Cat meta and Desciptions
				  $results = $wpdb->get_results( $bsb_amenity_cat_desc_query );
				  echo bsb_get_amenity_cat_reults($results);
				  
				  //Output another JS variable with Helper Info meta and Desciptions
				  $results = $wpdb->get_results( $bsb_info_boxes_query );
				  if($results) echo bsb_info_boxes_reults($results);
				  
				  //Debugging
				  //echo "/* bsb_amenity_query:\n\n" . $bsb_amenity_query ."*/ \n\n";
				  //echo "BSB_GOOGLE_APIKEY = \"" .BSB_GOOGLE_APIKEY. "\";\n\n";
				  
				  exit();
	            }//post callback function
			  ) //array
       );// register_rest_route	 
	  
	} //action function
	); // add action

/**
 * [LDM] Loop thru query results and out put "amenities" media info into a JS variable
 *
 */
function bsb_get_amenity_reults($results){
  $output = "bsb_amenities = {\n";
  $cat_last = '';
  //while($row = mysqli_fetch_assoc($results)) {
  foreach( $results as $key => $row) {
    $size_factor = $row->size_factor;
	//if( $size_factor = 1 ) $size_factor = 3;
	//if( $size_factor = 3 ) $size_factor = 0;
	$desc= preg_replace('(\n|\r\n)', "<br>", $row->description);
	$alt_text = esc_attr($row->title);
	$model =  "{id:\"{$row->ID}\",title:\"{$row->title}\",description:\"{$desc}\",thumb_url_full:\"{$row->thumb_url}\",model_url_full:\"{$row->model_url}\",alt_text:\"{$alt_text}\",size_factor:\"{$size_factor}\"}";
    
	$cat_current = $row->category;
	if( $cat_last !=  $cat_current ){ //start of new category
	  $output = preg_replace('/,$/', "", $output);  //finish up cat, remove comma from last model object in cat
	  if($cat_last != ''){ $output = $output. "\n],";} //end cat (but not first cat)
	  $cat_last =  $cat_current; //start new cat
      	
	  $output = $output. "\n\"" . $row->category . "\": [";
	}
	$output = $output. "\n". $model .",";
  }
  $output = preg_replace('/,$/', "\n]};\n", $output); //final cat
  return $output;
}




function bsb_get_amenity_cat_reults($results){
  $output = "\nbsb_amenities_cat_meta = {\n";
  foreach( $results as $key => $row) {
    $desc= preg_replace('(\n|\r\n)', "<br>", $row->description);

	$output = $output. "\"{$row->category}\" : {description:\"{$desc}\",cat_id:{$row->cat_id},parent_id:{$row->parent_id},slug:\"{$row->cat_slug}\" },\n";
  }
  $output = preg_replace('/,$/', "\n};\n", $output); //final cat
  return $output;
}

/** For info sections on builder pages */
function bsb_custom_post_type() {
	register_post_type('bsb_info_boxes',
		array(
			'labels'      => array(
				'name'          => __( 'BSB Info Boxes', 'textdomain' ),
				'singular_name' => __( 'BSB Info Box', 'textdomain' ),
			),
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => array( 'slug' => 'bsbinfo' ), // my custom slug
			'exclude_from_search' => true,
			'supports' => array( 'title', 'editor', 'revisions' ),
		)
	);
}
add_action('init', 'bsb_custom_post_type');


function bsb_info_boxes_reults($results){
  $output = "\nbsb_info_boxes_meta = {\n";
  foreach( $results as $key => $row) {
    //$desc= preg_replace('(\n|\r\n)', "<br>", $row->post_content);
	$desc= preg_replace('(\")', "\\\"", $row->post_content);
	$desc= preg_replace('(\n|\r\n)', "<br>", $desc);

    $output = $output. "\"{$row->post_name}\" : \"{$desc}\",\n";
	//$output = $output. "\"{$row->post_name}\" : {content:\"{$desc}\" },\n";
  }
  $output = preg_replace('/,$/', "\n};\n", $output); //final cat
  return $output;
}

add_action( 'after_setup_theme', 'my_child_theme_setup', 100 );
function my_child_theme_setup() {
	add_post_type_support( 'attachment', 'thumbnail' ); //attachment is post_type for built in Media Library
}
