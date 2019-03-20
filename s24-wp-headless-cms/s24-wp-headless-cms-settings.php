<?php

add_action('admin_menu', 's24_cache_create_plugin_settings_page');


function s24_cache_create_plugin_settings_page()
{
    // Add the menu item and page
    $page_title = 'Studio 24 Clear Cache Settings Page';
    $menu_title = 'S24 Wp Headless Cms';
    $capability = 'manage_options';
    $slug = 's24_wp_headless_cms';
    $callback = 's24_cache_plugin_settings_page_content';
    $icon = 'dashicons-admin-plugins';
    $position = 100;

    // TODO remove one or the other
    add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
    add_submenu_page('options-general.php', $page_title, $menu_title, $capability, $slug, $callback);
}

function s24_cache_plugin_settings_page_content()
{ ?>
    <div class="wrap">
        <h2>Studio 24 Clear Cache Settings Page</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('s24_wp_headless_cms');
            do_settings_sections('s24_wp_headless_cms');
            submit_button();
            ?>
        </form>
    </div>
    <div>
        <!-- todo send request to clear cache -->
        <button class="button button-secondary" onclick="{console.log('hello cache')}" >Clear all cache</button>
    </div>
    <?php
}

add_action('admin_init', 's24_cache_setup_sections');

function s24_cache_setup_sections()
{
    add_settings_section('s24_cache_varnish_options', 'Varnish options', 's24_cache_sections_callback', 's24_wp_headless_cms');
}

function s24_cache_sections_callback($arguments)
{
    switch ($arguments['id']) {
        case 's24_cache_varnish_options':
            // echo more info
            break;
    }
}

add_action('admin_init', 's24_cache_setup_varnish_fields');

function s24_cache_setup_varnish_fields()
{
    add_settings_field('s24_cache_isVarnish', 'Varnish is installed on the server:', 'is_varnish_field', 's24_wp_headless_cms', 's24_cache_varnish_options');
    register_setting('s24_wp_headless_cms', 's24_cache_isVarnish');
}

function is_varnish_field($arguments)
{
    ?>
    <input name="s24_cache_isVarnish" id="s24_cache_isVarnish" type="checkbox" value="1" <?php checked(1, get_option('s24_cache_isVarnish')) ?> />
    <?php
}
