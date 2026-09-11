<?php

namespace WooFieldBuilder\Frontend;

use WooFieldBuilder\Support\FieldRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renders custom fields on the single product page, before the add-to-cart button.
 */
class ProductFieldRenderer
{
    private FieldRepository $field_repository;

    public function __construct(FieldRepository $field_repository)
    {
        $this->field_repository = $field_repository;
    }

    public function register_hooks(): void
    {
        add_action('woocommerce_before_add_to_cart_button', [$this, 'render_fields']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(): void
    {
        if (!function_exists('is_product') || !is_product()) {
            return;
        }

        wp_enqueue_style('wfb-frontend', WFB_PLUGIN_URL . 'assets/css/frontend.css', [], WFB_VERSION);
    }

    public function render_fields(): void
    {
        global $product;

        if (!$product instanceof \WC_Product) {
            return;
        }

        $fields = $this->field_repository->get_fields_for_product($product->get_id());

        if (empty($fields)) {
            return;
        }

        $registry = $this->field_repository->get_field_type_registry();

        echo '<div class="wfb-fields">';

        foreach ($fields as $field) {
            if (empty($field['key'])) {
                continue;
            }

            $type = $registry->get($field['type']);

            if (!$type) {
                continue;
            }

            $submitted_value = isset($_POST[$field['key']]) ? wp_unslash($_POST[$field['key']]) : '';

            $type->render_frontend($field, $submitted_value);
        }

        echo '</div>';

        /**
         * Fires after Field Builder product fields are rendered, for themes/
         * plugins that want to add markup right after them.
         *
         * @param \WC_Product $product
         * @param array       $fields
         */
        do_action('wfb_after_product_fields', $product, $fields);
    }
}
