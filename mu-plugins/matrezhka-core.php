<?php
/**
 * Plugin Name: Matrezhka Core (MU)
 * Description: Core content structure for the Matrezhka news portal.
 * Author: Matrezhka Team
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register "city" taxonomy for posts.
 */
function matrezhka_register_city_taxonomy()
{
    register_taxonomy('city', array('post'), array(
        'labels' => array(
            'name'          => 'Города',
            'singular_name' => 'Город',
        ),
        'public'        => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'rest_base'     => 'city',
        'hierarchical'  => false,
        'rewrite'       => array('slug' => 'city'),
    ));
}
add_action('init', 'matrezhka_register_city_taxonomy', 0);

/**
 * Ensure required default post categories exist.
 * Runs only in admin/CLI contexts to avoid front-end overhead.
 */
function matrezhka_ensure_default_categories()
{
    if (!is_admin() && !(defined('WP_CLI') && WP_CLI)) {
        return;
    }

    $categories = array(
        'novosti'       => 'Новости',
        'intervyu'      => 'Интервью',
        'shkola'        => 'Школа',
        'kino-igry'     => 'Кино и игры',
        'sport'         => 'Спорт',
        'tvorchestvo'   => 'Творчество',
        'puteshestviya' => 'Путешествия',
        'hobbi'         => 'Хобби',
    );

    foreach ($categories as $slug => $name) {
        $existing = get_term_by('slug', $slug, 'category');
        if ($existing && !is_wp_error($existing)) {
            continue;
        }

        $result = wp_insert_term($name, 'category', array('slug' => $slug));
        if (is_wp_error($result) && 'term_exists' !== $result->get_error_code()) {
            error_log('Matrezhka category creation failed for slug: ' . $slug);
        }
    }
}
add_action('init', 'matrezhka_ensure_default_categories', 20);
