<?php

namespace WooFieldBuilder\Frontend;

use WooFieldBuilder\Support\FieldRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validates submitted field values on add-to-cart, stores them on the cart
 * item, and displays them in the cart/mini-cart/checkout review.
 */
class CartHandler
{
    public const CART_ITEM_DATA_KEY = 'wfb_field_values';

    private FieldRepository $field_repository;

    public function __construct(FieldRepository $field_repository)
    {
        $this->field_repository = $field_repository;
    }

    public function register_hooks(): void
    {
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_fields'], 10, 2);
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_cart_item_data'], 10, 2);
        add_filter('woocommerce_get_item_data', [$this, 'display_cart_item_data'], 10, 2);
    }

    public function validate_fields(bool $passed, int $product_id): bool
    {
        $fields = $this->field_repository->get_fields_for_product($product_id);
        $registry = $this->field_repository->get_field_type_registry();

        foreach ($fields as $field) {
            if (empty($field['key'])) {
                continue;
            }

            $type = $registry->get($field['type']);

            if (!$type) {
                continue;
            }

            $raw_value = $_POST[$field['key']] ?? null;
            $error     = $type->validate($raw_value, $field);

            if (null !== $error) {
                wc_add_notice($error, 'error');
                $passed = false;
            }
        }

        return $passed;
    }

    public function add_cart_item_data(array $cart_item_data, int $product_id): array
    {
        $fields   = $this->field_repository->get_fields_for_product($product_id);
        $registry = $this->field_repository->get_field_type_registry();
        $values   = [];

        foreach ($fields as $field) {
            if (empty($field['key']) || !isset($_POST[$field['key']])) {
                continue;
            }

            $type = $registry->get($field['type']);

            if (!$type) {
                continue;
            }

            $values[$field['key']] = [
                'label' => $field['label'],
                'value' => $type->sanitize($_POST[$field['key']]),
            ];
        }

        if (!empty($values)) {
            $cart_item_data[self::CART_ITEM_DATA_KEY] = $values;
            // Ensures WooCommerce treats different field values as separate cart line items.
            $cart_item_data['unique_key'] = md5(microtime() . wp_json_encode($values));
        }

        return $cart_item_data;
    }

    public function display_cart_item_data(array $item_data, array $cart_item): array
    {
        if (empty($cart_item[self::CART_ITEM_DATA_KEY])) {
            return $item_data;
        }

        $registry = $this->field_repository->get_field_type_registry();

        foreach ($cart_item[self::CART_ITEM_DATA_KEY] as $key => $entry) {
            $field = $this->field_repository->get_field_by_key($key);
            $type  = $field ? $registry->get($field['type']) : null;

            $display_value = $type && $field
                ? $type->format_value_for_display($entry['value'], $field)
                : (is_array($entry['value']) ? implode(', ', $entry['value']) : (string) $entry['value']);

            if ('' === $display_value) {
                continue;
            }

            $item_data[] = [
                'name'  => $entry['label'],
                'value' => $display_value,
            ];
        }

        return $item_data;
    }
}
