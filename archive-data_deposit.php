<?php

echo $GLOBALS['wp_query']->request;

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();
Timber::render('templates/search/search.twig', $context);
