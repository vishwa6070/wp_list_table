<?php
/**
 * Plugin Name:     	WP list Table
 * Plugin URI:      	https://ldninjas.com/
 * Description:     	This plugin have been developed to learn wp list table concept in wordpress.
 * Version:         	1.0
 * Author:          	https://ldninjas.com/
 * Author URI:      	https://ldninjas.com/
 * Text Domain:     	wp-list-table
 */

if( ! defined( 'ABSPATH' ) ) exit;

define( 'WLT_DIR', plugin_dir_path ( __FILE__ ) );
define( 'WLT_INCLUDES_DIR', trailingslashit ( WLT_DIR . 'includes' ) );
define( 'WLT_TEXT_DOMAIN', 'wp-list-table' );

/**
 * Add WP list table admin menu
 */
add_action( 'admin_menu', 'wlt_create_admin_menu' );

function wlt_create_admin_menu() {

    add_menu_page( 
        __( 'WP List Table', WLT_TEXT_DOMAIN ), 
        __( 'WP List Table', WLT_TEXT_DOMAIN ), 
        'manage_options', 
        'wp-list-table', 
        'wlt_create_admin_menu_cb', 
        '', 
        2 
    );
}

/**
 * WP list table menu page data/content
 */
function wlt_create_admin_menu_cb() {

    if( file_exists( WLT_INCLUDES_DIR . 'wp-list-table-data.php' ) ) {

        require_once WLT_INCLUDES_DIR . 'wp-list-table-data.php';
    }
}

/**
 * Enqueue a script in the WordPress admin on edit.php.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function wlt_enqueue_admin_script( $hook ) {

    wp_enqueue_style( 'wlt-backend-css', plugins_url( 'includes/css/wlt-backend.css',__FILE__ ) );
    wp_enqueue_script( 'wlt-backend-js', plugin_dir_url( __FILE__ ) . 'includes/js/wlt-backend.js', array(), '1.0' );

    wp_localize_script( 'wlt-backend-js', 'wlt',  

        array(
            'ajax_url'  => admin_url( 'admin-ajax.php' )
        )
    );

}
add_action( 'admin_enqueue_scripts', 'wlt_enqueue_admin_script' );

add_action( 'wp_ajax_wlt_delete_post', 'wlt_delete_post_data' );
function wlt_delete_post_data() {

    $post_id = isset( $_POST['wlt_post_id'] ) ? (int) $_POST['wlt_post_id'] : 0;
    if( ! $post_id ) {
        echo __( 'Post ID not found', WLT_TEXT_DOMAIN );

        wp_die();
    }

    wp_delete_post( $post_id );

    wp_die();
}

/**
 * Delete multiple data using bulk action 
 */
add_action( 'wp_ajax_wlt_multiple_delete_post', 'wlt_multiple_delete_post_data' );
function wlt_multiple_delete_post_data() {

    $post_ids = isset( $_POST['post_ids'] ) ? $_POST['post_ids'] : [];    
    if( $post_ids ) {

        foreach( $post_ids as $post_id ) {

            wp_delete_post( $post_id );
        }
    }
    

    wp_die();
}

/**
 * Create wp list table edit popUP
 */
add_action( 'in_admin_footer', 'wlt_create_edit_popup' );
function wlt_create_edit_popup() {

    add_thickbox();
    ?>
    <div id="wlt-content-id" style="display:none;">
        <p class="wlt-popup">
          <label><?php _e( 'Post Name :' ); ?></label>
          <input type="text" class="input-text wlt-edit-post-name">
        </p>
        <p class="wlt-popup">
          <label id="wlt-content"><?php _e( 'Post Content :' ); ?></label>
          <textarea class="wlt-edit-post-content"></textarea>
        </p>
        <p class="wlt-popup">
          <label><?php _e( 'Post Categories :' ); ?></label>
          <select>
              <option><?php _e( 'Select Category', WLT_TEXT_DOMAIN ); ?></option>
              <?php 
                $taxonomies = get_terms( array(
                    'taxonomy'   => 'category',
                    'hide_empty' => false
                ) );
                if( $taxonomies ) {

                    foreach( $taxonomies as $cat ) {
                        ?>
                        <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                        <?php
                    }
                }
              ?>
          </select>
        </p>

        <p class="wlt-popup">
          <label><?php _e( 'Post Tags :' ); ?></label>
          <select>
              <option><?php _e( 'Select Tag', WLT_TEXT_DOMAIN ); ?></option>
              <?php 
                $taxonomies = get_terms( array(
                    'taxonomy'   => 'post_tag',
                    'hide_empty' => false
                ) );
                if( $taxonomies ) {

                    foreach( $taxonomies as $tag ) {
                        ?>
                        <option value="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></option>
                        <?php
                    }
                }
              ?>
          </select>
        </p>

        <input class="wlt-update-post button-primary" type="button" value="<?php _e( 'Update', WLT_TEXT_DOMAIN ); ?>">
    </div>
    <?php
}

/**
 * Update post data using AJax
 */
add_action( 'wp_ajax_wlt_edit_update_post', 'wlt_wlt_edit_update_post' );
function wlt_wlt_edit_update_post() {

    $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0; 
    $post_content = isset( $_POST['post_content'] ) ? sanitize_textarea_field( $_POST['post_content'] ) : ''; 
    $post_name = isset( $_POST['post_name'] ) ? sanitize_text_field( $_POST['post_name'] ) : ''; 

    $data = array(
      'ID'              => $post_id,
      'post_title'      => $post_name,
      'post_content'    => $post_content,
    );
     
    wp_update_post( $data );

    wp_die();
}
