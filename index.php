<?php
/**
 * The main template file.
 */

// set up page data
if ( is_singular() ) :
    $context = Timber::get_context();
    $context['post'] = new TimberPost();

else :
    $context = Timber::get_context();
    $context['posts'] = Timber::get_posts();

endif;

// set up template path
if ( is_single() ) :
    $template = 'single';

elseif ( is_page() ) :
    $template = 'page';

elseif ( is_category() ) :
    $context['archive_title'] = get_cat_name( get_query_var('cat') );
    $context['archive_description'] = term_description();
    $context['page'] = 'archive';
    $template = 'archive';

elseif ( is_tag() ) :
    $context['archive_title'] = get_term_name( get_query_var('tag_id') );
    $context['archive_description'] = term_description();
    $context['page'] = 'archive';
    $template = 'archive';

elseif ( is_author() ) :
    $context['archive_title'] = get_the_author();
    $context['page'] = 'archive';
    $template = 'archive';
endif;

// render using Twig template index.twig
Timber::render('templates/' . $template . '.twig', $context );
