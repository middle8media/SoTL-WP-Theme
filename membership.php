<?php
/**
 * Template Name: Membership
 */
?>

<?php
$context = Timber::get_context();
$context['post'] = new TimberPost();

Timber::render('templates/membership.twig', $context);
