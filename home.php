<?php
/**
 * Template Name: Home
 */
?>

<?php

$args = array(
    'post_type' => 'data_deposit',
    'post_status' => 'publish',
    'posts_per_page' => 3,
);

$context = Timber::get_context();
$context['dataDeposits'] = Timber::get_posts($args);
$context['news'] = Timber::get_posts('post_type=news');

Timber::render('templates/home.twig', $context);
