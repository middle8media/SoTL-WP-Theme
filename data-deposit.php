<?php
/**
 * Template Name: Data Deposit
 */
?>

<?php


// $context['cel_data_deposit_form'] = TimberHelper::function_wrapper( 'cel_data_deposit_form', array(
//     'post_id' => 'new',
//     // PUT IN YOUR OWN FIELD GROUP ID(s)
//     'field_groups'       => array(37),
//     'form'               => true,
//     'return'             => '%post_url%',
//     'html_before_fields' => '',
//     'html_after_fields'  => '',
//     'submit_value'       => 'Submit Data',
//     'updated_message'    => 'Saved!'
// ) );
//
// acf_form($new_post);


// Bail if not logged in or able to post
// if ( ! ( is_user_logged_in()|| current_user_can('publish_posts') ) ) {
//     echo '<p>You must be a registered author to post.</p>';
//     return;
// }
//
// $new_post = array(
//     'form_attributes'    => array('id' => 'acf-form-hidden'), // this is a hack, needed to have the $new_post array in this file but not dispaly the form that it rendered. The actual form is rendered in data-deposit.twig via custom function in functions.php
//     'post_id'            => 'new', // Create a new post
//     // PUT IN YOUR OWN FIELD GROUP ID(s)
//     'field_groups'       => array(37), // Create post field group ID(s)
//     'form'               => true,
//     'return'             => '%post_url%', // Redirect to new post url
//     'html_before_fields' => '',
//     'html_after_fields'  => '',
//     'submit_value'       => 'Submit Data',
//     'updated_message'    => 'Saved!'
// );
// acf_form($new_post);


$context = Timber::get_context();

Timber::render('templates/data-deposits/data-deposit.twig', $context);
