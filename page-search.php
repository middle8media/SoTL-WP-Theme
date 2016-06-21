<?php
/**
 * Template Name: Search
 */
?>

<?php

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();
Timber::render('templates/search/page-search.twig', $context);
