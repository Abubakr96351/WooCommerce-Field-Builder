<?php

namespace WooFieldBuilder\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Copies field values from the cart item into the order line item, so they
 * show up automatically in the admin order view and default WC emails.
 */
class OrderHandler
{
    public function register_hooks(): void
    {
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_order_item_meta'], 10, 4);
    }

    /**
     * @param \WC_Order_Item_Product $item
     * @param string                 $cart_item_key
     * @param array                  $values
     * @param \WC_Order              $order
     */
    public function add_order_item_meta($item, string $cart_item_key, array $values, $order): void
    {
        if (empty($values[CartHandler::CART_ITEM_DATA_KEY])) {
            return;
        }

        foreach ($values[CartHandler::CART_ITEM_DATA_KEY] as $entry) {
            $value = is_array($entry['value']) ? implode(', ', $entry['value']) : $entry['value'];

            if ('' === (string) $value) {
                continue;
            }

            $item->add_meta_data($entry['label'], $value, true);
        }
    }
}
