<?php
/**
 * Plugin Name: Matrezhka API (MU)
 * Description: JWT payload extensions and custom REST endpoints for mobile app.
 * Author: Matrezhka Team
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add user roles to JWT auth response payload.
 */
add_filter('jwt_auth_token_before_dispatch', function ($data, $user) {
    if ($user instanceof WP_User) {
        $data['roles'] = array_values($user->roles);
    } else {
        $data['roles'] = array();
    }

    return $data;
}, 10, 2);

/**
 * Build ads payload for the mobile app.
 * Keep this function as a single place for future Ad Inserter integration.
 *
 * @return array<int, array<string, mixed>>
 */
function matrezhka_get_ads_payload()
{
    // TODO: Replace with real Ad Inserter blocks loading logic.
    return array(
        array(
            'id' => 1,
            'type' => 'banner',
            'content' => '<img src="https://placehold.co/1080x320?text=Matrezhka+Ad" alt="Ad" />',
            'position' => 'header',
            'active' => true,
        ),
    );
}

add_action('rest_api_init', function () {
    register_rest_route('matrezhka/v1', '/ads', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => function () {
            return rest_ensure_response(matrezhka_get_ads_payload());
        },
        'permission_callback' => '__return_true',
    ));
});
