<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/timber.php', 			// Twig magic
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

require_once('vendor/autoload.php');

// Removes admin menu for users
add_action('admin_head', 'cel_wpse_52099_script_enqueuer');
function cel_wpse_52099_script_enqueuer(){
    if(!current_user_can('administrator')) {
        echo <<<HTML
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                $('#adminmenuback, #adminmenuwrap, #wp-admin-bar-comments, #wp-admin-bar-new-content, #wp-admin-bar-menu-toggle').remove();
            });
        </script>
HTML;
    }
}

// removes admin bar on front-end
show_admin_bar( false );

// Remove Unwanted Admin Menu Items
// function cel_remove_admin_menu_items() {
// 	$remove_menu_items = array(__('Posts'), __('Links'), __('Comments'));
// 	global $menu;
// 	end ($menu);
// 	while (prev($menu)){
// 		$item = explode(' ',$menu[key($menu)][0]);
// 		if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
// 		unset($menu[key($menu)]);}
// 	}
// }
// add_action('admin_menu', 'cel_remove_admin_menu_items');

// This adds a new user to the "Pending" group upon submission of membership registration form.
add_action('user_register', 'cel_user_register' );

function cel_user_register ( $user_id ) {
        if ($user_id != null) {
                if ( $group = Groups_Group::read_by_name( 'Pending' ) ) {
                        $result = Groups_User_Group::create( array( "user_id"=>$user_id, "group_id"=>$group->group_id ) );
                }
        }
}

// // check if you actually have drafts; also avoids extra '|' separator
// if (isset($views['draft'])) {
//     // 'Drafts' should be added (and come first) if you don't want to end up with 'Unavailables'
//     $views['draft'] = str_replace(array('Drafts','Draft'), 'Unavailable', $views['draft']);
// }

// Register Custom Status
function cel_custom_post_status(){
    register_post_status( 'declined', array(
        'label'                     => _x( 'Declined', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Declined <span class="count">(%s)</span>', 'Declined <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'cel_custom_post_status' );

// adds "Decline" to status dropdown
function cel_append_post_status_list(){
    global $post;
    $complete = '';
    $label = '';
    if($post->post_type == 'post'){
        if($post->post_status == 'declined'){
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> Declined</span>';
        }
     }
}
add_action('admin_footer-post.php', 'cel_append_post_status_list');

// adds "Declined" to status dropdown in "Quick Edit" on "All Data Deposit" list
function cel_declined_status_into_inline_edit() {
    echo "
        <script>
            jQuery(document).ready( function() {
                jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"declined\">Decline</option>' );
            });
        </script>
    ";
}
add_action('admin_footer-edit.php','cel_declined_status_into_inline_edit');

// Displays declined status next to post title
function cel_display_status_label( $statuses ) {
    global $post; // we need it to check current post status
    if( get_query_var( 'post_status' ) != 'declined' ){ // not for pages with all posts of this status
        if( $post->post_status == 'declined' ){
            return array('Declined'); // returning our status label
        }
    }
    return $statuses; // returning the array with default statuses
}
add_filter( 'display_post_states', 'cel_display_status_label' );

// ACF Options Page
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page();
    acf_add_options_sub_page('Admin Contact');
    acf_add_options_sub_page('Header');
    acf_add_options_sub_page('Footer');

}

// Timber - use AFC options sitewide
function cel_timber_context( $context ) {
    $context['options'] = get_fields('option');
    return $context;
}
add_filter( 'timber_context', 'cel_timber_context'  );

// Hide Subgroup taxonomy from Member
// if (is_admin()) :
// function cel_remove_custom_taxonomy() {
//     if( !current_user_can('manage_options') ) {
// 	       remove_meta_box('tagsdiv-', subgroups, data_deposit, 'side' );
//     }
// }
// add_action( 'admin_menu', 'cel_remove_custom_taxonomy' );
// endif;

// Adds a custom WYSIWYG toolbar to AF
function cel_custom_acf_toolbars( $toolbars ) {

    // Add a new toolbar called "Very Simple"
    // - this toolbar has only 1 row of buttons
    $toolbars['Simple' ][1] = array('bullist');

    // remove the 'Basic' toolbar completely
    unset( $toolbars['Basic' ] );
    unset( $toolbars['Full' ] );

    // return $toolbars - IMPORTANT!
    return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars' , 'cel_custom_acf_toolbars'  );

// Adds custom class to iFrame video embeds
function cel_custom_class_to_wordpress_iframe_video( $html ) {

    if( preg_match('/(youtube.com)/', $html) ){ // if youtube video
    return str_replace('<iframe', '<iframe class="youtube-video"', $html); // add my custom class "youtube-video"
    }

    elseif( preg_match('/(vimeo.com)/', $html) ){ // if vimeo video
    return str_replace('<iframe', '<iframe class="vimeo-video"', $html); // add my custom class "vimeo-video"
    }

    else{
    return $html;
    }

}
add_filter('embed_oembed_html', 'cel_custom_class_to_wordpress_iframe_video', 99, 4);

// Redircts users to home page after LogicException/* redirect users to front page after login */
function cel_redirect_to_front_page() {
global $redirect_to;
if (!isset($_GET['redirect_to'])) {
$redirect_to = get_option('siteurl');
}
}
add_action('login_form', 'cel_redirect_to_front_page');

//* Redirect WordPress Logout to Home Page
add_action('wp_logout',create_function('','wp_redirect(home_url());exit();'));

// custom login form function
function cel_wp_login_form( $args = array() ) {
    $defaults = array(
        'echo' => true,
        // Default 'redirect' value takes the user back to the request URI.
        'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'form_id' => 'loginform',
        'label_username' => __( 'Username or Email' ),
        'label_password' => __( 'Password' ),
        'label_log_in' => __( 'Login' ),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_submit' => 'wp-submit',
        'remember' => true,
        'value_username' => '',
    );

    /**
     * Filter the default login form output arguments.
     *
     * @since 3.0.0
     *
     * @see wp_login_form()
     *
     * @param array $defaults An array of default login form arguments.
     */
    $args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

    $form = '
        <form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" class="account-login" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
            <p class="login-username">
                <input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input"  placeholder="Username" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
            </p>

            <p class="login-password">
                <input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input"  placeholder="Password" value="" size="20" />
            </p>

            <p class="login-submit">
                <input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="btn btn-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
                <input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
            </p>
            <p>
            <a href="/wp-login.php?action=lostpassword">Forgot Password?</a> or <a href="/wp-login.php?action=register">Don\'t Have an Account</a>
            </p>
        </form>';

    if ( $args['echo'] )
        echo $form;
    else
        return $form;
}

// custom login page styles
function cel_custom_login() {
echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/dist/styles/main.css" />';
}
add_action('login_head', 'cel_custom_login');

// display GF Member Registraton Form (on Membership page)
function unpack_member_registration_form($post_id) {
  $data = get_field('membership_registration_form', $post_id);

  print_r(gravity_form( 1, $display_title = false, $display_description = false, $display_inactive = false, $field_values = null, $ajax = false, $echo = true ));
}

// update GF Member Update Form (on Membership page)
function unpack_member_update_form($post_id) {
  $data = get_field('member_update_form', $post_id);

  print_r(gravity_form( 6, $display_title = false, $display_description = false, $display_inactive = false, $field_values = null, $ajax = false, $echo = true ));
}

// Admin css & js
function admin_scripts() {
    if(!current_user_can('administrator')) {
        wp_enqueue_style('admin_style', get_template_directory_uri() . '/assets/admin/admin.css', false, null);
        wp_enqueue_script( 'admin_script', get_template_directory_uri() . '/assets/admin/admin.js');
    }
}
add_action( 'admin_enqueue_scripts', 'admin_scripts');

// USER META FIELDS IN PROFILE PAGE
// create custom user meta fields
function cel_my_show_extra_profile_fields( $user ) { ?>

    <h3>Extra profile information</h3>

    <table class="form-table">

        <tr>
            <th><label for="affiliation">Affiliation</label></th>

            <td>
                <input type="text" name="affiliation" id="affiliation" value="<?php echo esc_attr( get_the_author_meta( 'affiliation', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>

        <tr>
            <th><label for="title">Professional Title</label></th>

            <td>
                <input type="text" name="title" id="title" value="<?php echo esc_attr( get_the_author_meta( 'title', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>

    </table>
<?php }

add_action( 'show_user_profile', 'cel_my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'cel_my_show_extra_profile_fields' );

// save custom user meta fields
function cel_my_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    update_usermeta( $user_id, 'affiliation', $_POST['affiliation'] );
    update_usermeta( $user_id, 'title', $_POST['title'] );
}

add_action( 'personal_options_update', 'cel_my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'cel_my_save_extra_profile_fields' );


function cel_modify_profile_fields($profile_fields) {

    // Remove old fields
    unset($profile_fields['url']);

    return $profile_fields;
}
add_filter('user_contactmethods', 'cel_modify_profile_fields');




//
// function cel_acf_form_head() {
//     // Bail if not logged in or not able to post
//     if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
//         return;
//     }
//     acf_form_head();
// }
// add_action( 'get_header', 'cel_acf_form_head', 1 );
//
// //
// function cel_deregister_admin_styles() {
//     // Bail if not logged in or not able to post
//     if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
//         return;
//     }
//     wp_deregister_style( 'wp-admin' );
// }
// add_action( 'wp_print_styles', 'cel_deregister_admin_styles', 999 );
//
// //
// function cel_data_deposit_form() {
//     // Bail if not logged in or able to post
//         if ( ! ( is_user_logged_in()|| current_user_can('publish_posts') ) ) {
//             echo '<p>You must be a registered author to post.</p>';
//             return;
//         }
//
//         $new_post = array(
//             'post_id'            => 'new', // Create a new post
//             // PUT IN YOUR OWN FIELD GROUP ID(s)
//             'field_groups'       => array(37), // Create post field group ID(s)
//             'post_title'         => true,
//             'form'               => true,
//             'return'             => '%post_url%', // Redirect to new post url
//             'html_before_fields' => '',
//             'html_after_fields'  => '',
//             'submit_value'       => 'Submit Data',
//             'updated_message'    => 'Saved!'
//         );
//         acf_form($new_post);
// }
//
// function cel_do_pre_save_post( $post_id ) {
// 	// Bail if not logged in or not able to post
// 	if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
// 		return;
// 	}
// 	// check if this is to be a new post
// 	if( $post_id != 'new' ) {
// 		return $post_id;
// 	}
// 	// Create a new post
// 	$post = array(
// 		'post_type'     => 'data_deposit', // Your post type ( post, page, custom post type )
// 		'post_status'   => 'pending', // (publish, draft, private, etc.)
// 		'first_name'    => $_POST['acf']['field_572a151b9e10b'],
//         'first_name'    => $_POST['acf']['field_572a151b9e10b'],
//         'last_name'    => $_POST['acf']['field_572a16d84a7c6'],
//
// 	);
// 	// insert the post
// 	$post_id = wp_insert_post( $post );
// 	// Save the fields to the post
// 	do_action( 'acf/save_post' , $post_id );
// 	return $post_id;
// }
// add_filter('acf/pre_save_post' , 'cel_do_pre_save_post' );

// function my_random_thing() {
// 	global $pagenow;
// 	if( $pagenow == 'post.php' )
// 		echo '<div class="navbar-header">
//             <a class="navbar-brand" href="/">
//                 <img src="http://local.wordpress.dev/wp-content/uploads/2016/05/cel-logo.png" class="my-thumb-class" alt="Image for {{post.title}}"/>
//             </a>
//         </div>';
// }
//
// add_action( 'admin_notices', 'my_random_thing' );


// Custom query_posts
// function cel_my_home_query( $query ) {
//     if ( $query->is_main_query() && !is_admin() ) {
//         $query->set( 'post_type', array( 'data_deposit' ));
//     }
// }
// add_action( 'pre_get_posts', 'cel_my_home_query' );

// query_posts for data_deposit custom post type
function cel_search_filter($query) {
    if ( !is_admin() && $query->is_main_query() ) {

        if ($query->is_search) {
            $query->set(
                'post_type', array( 'data_deposit'),
                'post_status', 'publish'
            );
        }
    }
}

add_action('pre_get_posts','cel_search_filter');


function cel_custom_author_archive( &$query ) {
    if ($query->is_author) {
        $query->set( 'post_type', 'data_deposit' );
        $query->set( 'post_status', array('publish', 'draft', 'pending', 'declined' ) );
        $query->set( 'posts_per_page', 3 );
    }
}
add_action( 'pre_get_posts', 'cel_custom_author_archive' );
