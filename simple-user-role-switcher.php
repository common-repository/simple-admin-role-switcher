<?php
/*
Plugin Name: Simple Admin Role Switcher
Plugin URI: https://abcode.co.uk/plugins/simple-admin-role-switcher/
Description: Allows administrators to switch and view the site as different user roles without logging out, for frontend testing purposes only.
Version: 1.0
Author: Ash Brentnall | ABCode
Author URI: https://abcode.co.uk
License: GPL2
Text Domain: simple-admin-role-switcher
*/

if (!defined('ABSPATH')) {
    exit;
}

// Handle role switching via a cookie for frontend testing
function sars_switch_user_role($role) {
    if (current_user_can('administrator')) {
        setcookie('sars_view_as', sanitize_text_field($role), time() + 3600, '/');  // Expires in 1 hour
    }
}

// Apply the temporary role on the frontend
function sars_apply_role_switch() {
    if (isset($_COOKIE['sars_view_as'])) {
        $role = sanitize_text_field(wp_unslash($_COOKIE['sars_view_as']));
        if ($role === 'guest') {
            wp_set_current_user(0);  // Simulate being a guest
            add_filter('show_admin_bar', '__return_true');  // Ensure admin bar is visible for guests
            add_action('admin_bar_menu', 'sars_add_guest_toolbar_items', 100);  // Add guest admin bar
        } elseif (in_array($role, array_keys(wp_roles()->roles))) {
            wp_get_current_user()->roles = [$role];
            add_filter('show_admin_bar', '__return_true');  // Ensure admin bar is visible for other roles
        }
    }
}
add_action('template_redirect', 'sars_apply_role_switch');

// Add a role switcher to the admin toolbar with conditional new tab functionality
function sars_add_toolbar_items($wp_admin_bar) {
    if (current_user_can('administrator')) {
        $current_role = isset($_COOKIE['sars_view_as']) ? ucfirst(sanitize_text_field(wp_unslash($_COOKIE['sars_view_as']))) : 'Administrator';

        // Update the menu title to show the current view role
        $wp_admin_bar->add_node(array(
            'id'    => 'sars_role_switcher',
            'title' => 'Viewing Frontend As: ' . $current_role,
            'meta'  => array('class' => 'sars-role-switcher'),
        ));

        // Add menu items for each role, conditional on whether we're in the admin area
        foreach (wp_roles()->roles as $role => $details) {
            if (strtolower($current_role) === $role) {
                continue; // Skip the current role
            }

            $meta = array();
            // Only open a new tab if in admin area and switching to a different role
            if (is_admin()) {
                $meta['target'] = '_blank';
            }

            $wp_admin_bar->add_node(array(
                'id'     => 'sars_view_as_' . $role,
                'parent' => 'sars_role_switcher',
                'title'  => 'View as ' . ucfirst($role),
                'href'   => add_query_arg(array('sars_view_as' => $role, '_wpnonce' => wp_create_nonce('sars_switch_role_nonce'))),
                'meta'   => $meta,
            ));
        }

        // Add "View as Guest" option
        if (strtolower($current_role) !== 'guest') {
            $meta = array();
            if (is_admin()) {
                $meta['target'] = '_blank';  // Open in new tab only from admin
            }
            $wp_admin_bar->add_node(array(
                'id'     => 'sars_view_as_guest',
                'parent' => 'sars_role_switcher',
                'title'  => 'View as Guest',
                'href'   => add_query_arg('sars_view_as', 'guest'),
                'meta'   => $meta,
            ));
        }
    }
}
add_action('admin_bar_menu', 'sars_add_toolbar_items', 100);

// Handle role switching and refresh or open a new tab based on the situation
function sars_secure_role_switch() {
    if (!isset($_GET['sars_view_as'])) {
        return;
    }

    $role = sanitize_text_field(wp_unslash($_GET['sars_view_as']));

    // Verify nonce for logged-in users
    if ($role !== 'guest' && $role !== 'reset' && isset($_GET['_wpnonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'sars_switch_role_nonce')) {
        return;
    }

    // Handle resetting the role (switching back to admin)
    if ($role === 'reset') {
        setcookie('sars_view_as', '', time() - 3600, '/');
        // Refresh the page in admin area or frontend
        wp_redirect(remove_query_arg(array('sars_view_as', '_wpnonce')));
        exit;
    }

    // Handle guest role switching
    if ($role === 'guest') {
        sars_switch_user_role('guest');
        if (is_admin()) {
            // Redirect to the home page in a new tab when switching to guest from admin
            wp_redirect(home_url('/'));
        } else {
            // Refresh page when switching to guest from frontend
            wp_redirect(remove_query_arg(array('sars_view_as', '_wpnonce')));
        }
        exit;
    }

    // Handle switching to other roles
    if (in_array($role, array_keys(wp_roles()->roles))) {
        sars_switch_user_role($role);

        if (is_admin()) {
            // If switching from admin area to another role, use wp_redirect to open home page in new tab
            wp_redirect(home_url('/'));
            exit;
        } else {
            // On frontend, just refresh the current page
            wp_redirect(remove_query_arg(array('sars_view_as', '_wpnonce')));
            exit;
        }
    }
}
add_action('init', 'sars_secure_role_switch');

// Render a guest admin bar similar to backend admin bar
function sars_add_guest_toolbar_items($wp_admin_bar) {
    // Remove the 'Guest' user profile and search items
    $wp_admin_bar->remove_node('my-account');
    $wp_admin_bar->remove_node('search');

    // Reorder items to place 'Site Name' and 'Dashboard' before the role switcher
    $wp_admin_bar->add_node(array(
        'id'    => 'site-name',
        'title' => get_bloginfo('name'),
        'href'  => home_url(),
        'meta'  => array(
            'class' => 'ab-item',
        ),
    ));

    $wp_admin_bar->add_node(array(
        'id'    => 'dashboard',
        'title' => 'Dashboard',
        'href'  => admin_url(),
        'meta'  => array(
            'class' => 'ab-item',
        ),
    ));

    // Add the role switcher to the guest admin bar
    $wp_admin_bar->add_node(array(
        'id'    => 'sars_role_switcher_guest',
        'title' => 'Viewing Frontend As: Guest',
        'meta'  => array('class' => 'sars-role-switcher'),
    ));

    // Add menu items for each role to switch
    foreach (wp_roles()->roles as $role => $details) {
        if ($role === 'guest') {
            continue; // Skip the guest role if already in guest mode
        }
        $wp_admin_bar->add_node(array(
            'id'     => 'sars_view_as_guest_' . $role,
            'parent' => 'sars_role_switcher_guest',
            'title'  => 'View as ' . ucfirst($role),
            'href'   => add_query_arg('sars_view_as', $role),
        ));
    }
}

// Keep the admin bar visible when viewing as a guest or other roles
function sars_force_admin_bar_for_guest($show_admin_bar) {
    if (current_user_can('administrator') || isset($_COOKIE['sars_view_as'])) {
        return true; // Ensure the admin bar remains visible for the guest role or any switched role
    }
    return $show_admin_bar;
}
add_filter('show_admin_bar', 'sars_force_admin_bar_for_guest');

// Clear the role switching cookie on logout
function sars_clear_role_switch_on_logout() {
    setcookie('sars_view_as', '', time() - 3600, '/');  // Clear the cookie
}
add_action('wp_logout', 'sars_clear_role_switch_on_logout');

// Load plugin text domain for translations (optional)
function sars_load_textdomain() {
    load_plugin_textdomain('simple-admin-role-switcher', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'sars_load_textdomain');

// Clear the role switching cookie and revert any temporary roles when the plugin is uninstalled
function sars_uninstall() {
    // Clear the cookie
    setcookie('sars_view_as', '', time() - 3600, '/');
    // Ensure all users revert back to their original roles by clearing any role switches
    if (isset($_COOKIE['sars_view_as'])) {
        wp_get_current_user()->roles = array('administrator'); // Revert to admin role
    }
}
register_uninstall_hook(__FILE__, 'sars_uninstall');
