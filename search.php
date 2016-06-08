<?php

// $templates = array( 'search.twig', 'archive.twig', 'index.twig' );
// Timber::render( $templates, $context );

// $context = Timber::get_context();
// $context['dataDeposits'] = Timber::get_posts('post_type=data_deposit');

// Timber::render('templates/search/search.twig', $context);


// $context['queryTitle'] = 'results were found for the search for '. get_search_query();
// $context['search_query'] = $_GET['s'];
// $context['pagination'] = Timber::get_pagination();


// global $paged;
//     if (!isset($paged) || !$paged){
//         $paged = 1;
//     }
//     $args = array(
//         'post_type' => 'data_deposit',
//         'post_status' => 'publish',
//         'posts_per_page' => 3,
//         'paged' => $paged
//     );
//     query_posts($args);

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();
Timber::render('templates/search/search.twig', $context);
