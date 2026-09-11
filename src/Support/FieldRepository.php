<?php

namespace WooFieldBuilder\Support;

use WooFieldBuilder\Fields\FieldTypeRegistry;
use WooFieldBuilder\PostTypes\FieldPostType;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Reads field definitions from `wfb_field` posts/postmeta into plain arrays
 * consumed by field type renderers and handlers.
 */
class FieldRepository
{
    private FieldTypeRegistry $field_type_registry;

    public function __construct(FieldTypeRegistry $field_type_registry)
    {
        $this->field_type_registry = $field_type_registry;
    }

    /**
     * All published fields that apply to a given product: fields scoped to
     * "all" products, plus fields scoped to "specific" products that include
     * this product ID. Ordered by menu_order (admin-configurable priority).
     *
     * @return array<int, array>
     */
    public function get_fields_for_product(int $product_id): array
    {
        $posts = get_posts([
            'post_type'      => FieldPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'   => '_wfb_scope',
                    'value' => 'all',
                ],
                [
                    'relation' => 'AND',
                    [
                        'key'   => '_wfb_scope',
                        'value' => 'specific',
                    ],
                    [
                        'key'     => '_wfb_product_ids',
                        'value'   => $product_id,
                        'compare' => '=',
                    ],
                ],
            ],
        ]);

        $fields = [];

        foreach ($posts as $post) {
            $fields[] = $this->map_post_to_field($post->ID);
        }

        return $fields;
    }

    public function get_field_by_key(string $key): ?array
    {
        $posts = get_posts([
            'post_type'      => FieldPostType::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_key'       => '_wfb_key',
            'meta_value'     => $key,
        ]);

        if (empty($posts)) {
            return null;
        }

        return $this->map_post_to_field($posts[0]->ID);
    }

    public function get_field(int $post_id): ?array
    {
        $post = get_post($post_id);

        if (!$post || FieldPostType::POST_TYPE !== $post->post_type) {
            return null;
        }

        return $this->map_post_to_field($post_id);
    }

    private function map_post_to_field(int $post_id): array
    {
        $type = get_post_meta($post_id, '_wfb_type', true) ?: 'text';

        return [
            'id'          => $post_id,
            'key'         => get_post_meta($post_id, '_wfb_key', true),
            'type'        => $type,
            'label'       => get_the_title($post_id),
            'required'    => (bool) get_post_meta($post_id, '_wfb_required', true),
            'placeholder' => get_post_meta($post_id, '_wfb_placeholder', true),
            'options'     => get_post_meta($post_id, '_wfb_options', true) ?: [],
            'scope'       => get_post_meta($post_id, '_wfb_scope', true) ?: 'all',
            'product_ids' => array_map('absint', get_post_meta($post_id, '_wfb_product_ids')),
        ];
    }

    public function get_field_type_registry(): FieldTypeRegistry
    {
        return $this->field_type_registry;
    }
}
