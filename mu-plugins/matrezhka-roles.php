<?php
/**
 * Plugin Name: Matrezhka Roles (MU)
 * Description: Registers and maintains project roles/capabilities from the specification.
 * Author: Matrezhka Team
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return role definitions from the technical specification.
 *
 * @return array<string, array{name:string, capabilities:string[]}>
 */
function matrezhka_role_definitions()
{
    return array(
        'matre_author' => array(
            'name' => 'Автор',
            'capabilities' => array(
                'read',
                'create_posts',
                'edit_posts',
                'upload_files',
                'delete_posts',
            ),
        ),
        'matre_moderator' => array(
            'name' => 'Модератор',
            'capabilities' => array(
                'read',
                'create_posts',
                'edit_posts',
                'upload_files',
                'delete_posts',
                'edit_others_posts',
                'publish_posts',
                'moderate_comments',
            ),
        ),
        'matre_editor' => array(
            'name' => 'Редактор',
            'capabilities' => array(
                'read',
                'create_posts',
                'edit_posts',
                'upload_files',
                'delete_posts',
                'edit_others_posts',
                'publish_posts',
                'moderate_comments',
                'manage_categories',
                'edit_theme_options',
            ),
        ),
    );
}

/**
 * Ensure project roles exist and have exact capability sets.
 */
function matrezhka_sync_roles()
{
    $definitions = matrezhka_role_definitions();

    foreach ($definitions as $role_key => $definition) {
        $role = get_role($role_key);

        if (!$role) {
            add_role($role_key, $definition['name'], array('read' => true));
            $role = get_role($role_key);
        }

        if (!$role instanceof WP_Role) {
            continue;
        }

        $desired_caps = array_fill_keys($definition['capabilities'], true);
        $current_caps = is_array($role->capabilities) ? $role->capabilities : array();

        foreach ($current_caps as $cap => $granted) {
            if (!isset($desired_caps[$cap])) {
                $role->remove_cap($cap);
            }
        }

        foreach ($desired_caps as $cap => $granted) {
            $role->add_cap($cap, true);
        }
    }
}
add_action('init', 'matrezhka_sync_roles', 5);

/**
 * Hide default WP Author/Editor roles from role selectors to avoid duplicates
 * with project-specific roles that have the same translated labels.
 */
function matrezhka_filter_editable_roles($roles)
{
    unset($roles['author'], $roles['editor']);
    return $roles;
}
add_filter('editable_roles', 'matrezhka_filter_editable_roles');

/**
 * Hide "Other roles" UI from User Role Editor on user profile/add screens.
 */
add_filter('ure_show_additional_capabilities_section', '__return_false');
