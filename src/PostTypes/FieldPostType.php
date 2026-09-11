<?php

namespace WooFieldBuilder\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers the `wfb_field` custom post type used to store field definitions.
 */
class FieldPostType
{
    public const POST_TYPE = 'wfb_field';

    public function register_hooks(): void
    {
        add_action('init', [$this, 'register_post_type']);
    }

    public function register_post_type(): void
    {
        register_post_type(self::POST_TYPE, [
            'label'              => __('Product Fields', 'woo-field-builder'),
            'labels'             => [
                'name'               => __('Product Fields', 'woo-field-builder'),
                'singular_name'      => __('Product Field', 'woo-field-builder'),
                'add_new'            => __('Add New Field', 'woo-field-builder'),
                'add_new_item'       => __('Add New Field', 'woo-field-builder'),
                'edit_item'          => __('Edit Field', 'woo-field-builder'),
                'new_item'           => __('New Field', 'woo-field-builder'),
                'view_item'          => __('View Field', 'woo-field-builder'),
                'search_items'       => __('Search Fields', 'woo-field-builder'),
                'not_found'          => __('No fields found', 'woo-field-builder'),
                'not_found_in_trash' => __('No fields found in Trash', 'woo-field-builder'),
                'all_items'          => __('Product Fields', 'woo-field-builder'),
                'menu_name'          => __('Field Builder', 'woo-field-builder'),
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-forms',
            'menu_position'      => 56,
            'capability_type'    => 'post',
            'capabilities'       => [
                'edit_post'          => 'manage_woocommerce',
                'read_post'          => 'manage_woocommerce',
                'delete_post'        => 'manage_woocommerce',
                'edit_posts'         => 'manage_woocommerce',
                'edit_others_posts'  => 'manage_woocommerce',
                'publish_posts'      => 'manage_woocommerce',
                'read_private_posts' => 'manage_woocommerce',
            ],
            'map_meta_cap'       => true,
            'supports'           => ['title'],
            'has_archive'        => false,
            'rewrite'            => false,
            'show_in_rest'       => false,
            'exclude_from_search' => true,
        ]);
    }
}
