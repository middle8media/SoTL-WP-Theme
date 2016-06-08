<?php
$context = Timber::get_context();
$context['dataDeposits'] = Timber::get_posts('post_type=data_deposit');

Timber::render('templates/data-deposits/single-data.twig', $context);
