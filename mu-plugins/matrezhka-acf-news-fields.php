<?php
/**
 * Plugin Name: Matrezhka News ACF Fields (MU)
 * Description: Registers ACF fields required by the technical specification.
 * Author: Matrezhka Team
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_matrezhka_news_params',
        'title' => 'Параметры новости',
        'fields' => array(
            array(
                'key' => 'field_matrezhka_city',
                'label' => 'Город',
                'name' => 'city',
                'type' => 'taxonomy',
                'required' => 1,
                'taxonomy' => 'city',
                'field_type' => 'select',
                'allow_null' => 0,
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'show_in_rest' => 1,
            ),
            array(
                'key' => 'field_matrezhka_editor_pick',
                'label' => 'Выбор редакции',
                'name' => 'editor_pick',
                'type' => 'true_false',
                'required' => 0,
                'ui' => 1,
                'ui_on_text' => 'Да',
                'ui_off_text' => 'Нет',
                'message' => 'Выбор редакции',
                'default_value' => 0,
                'show_in_rest' => 1,
            ),
            array(
                'key' => 'field_matrezhka_breaking_news',
                'label' => 'Срочная новость',
                'name' => 'breaking_news',
                'type' => 'true_false',
                'required' => 0,
                'ui' => 1,
                'ui_on_text' => 'Да',
                'ui_off_text' => 'Нет',
                'message' => 'Срочная новость',
                'default_value' => 0,
                'show_in_rest' => 1,
            ),
            array(
                'key' => 'field_matrezhka_editor_notes',
                'label' => 'Правки редактора',
                'name' => 'editor_notes',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 4,
                'new_lines' => '',
                'show_in_rest' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
        'show_in_rest' => 1,
    ));
});
