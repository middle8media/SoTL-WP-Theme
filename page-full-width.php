<?php
/**
 * Template Name: Full Width
 */
?>

<?php
$context = Timber::get_context();
$context['post'] = new TimberPost();

Timber::render('templates/page.twig', $context);
