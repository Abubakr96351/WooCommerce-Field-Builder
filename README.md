# WooCommerce Field Builder

Custom product fields for WooCommerce. Phase 1: Product Fields only (no conditions,
pricing, or file uploads yet — see the roadmap below).

## Requirements

- PHP 7.4+
- WordPress 6.0+
- WooCommerce 7.0+

## Installation

1. Copy the `woo-field-builder` folder into `wp-content/plugins/`.
2. (Optional) Run `composer install` inside the plugin folder — not required,
   the plugin falls back to a built-in autoloader if `vendor/` is missing.
3. Activate **WooCommerce Field Builder** from Plugins in wp-admin.
4. A new **Field Builder** menu appears in the admin sidebar.

## Usage

1. Go to **Field Builder > Add New Field**.
2. Set the field title (shown to customers as the label), choose a **Field Type**,
   set a **Field Key** (unique machine name, e.g. `gift_message`), mark it
   **Required** if needed, and configure options (for Select/Multi-Select/
   Checkbox Group/Radio Group).
3. In the **Apply To** box, choose **All products** or **Specific products**
   (search and select one or more products).
4. Publish the field.
5. Visit the product page(s) it applies to — the field renders above the
   Add to Cart button.

## Manual test checklist

Run through this once you have a WordPress + WooCommerce environment available:

- [ ] Create a **Text** field, scope "All products". Confirm it renders on
      every product page.
- [ ] Create a **required Select** field scoped to one specific product, with
      2-3 options. Confirm it only renders on that product, and that
      submitting without a selection blocks add-to-cart with an error notice.
- [ ] Add the specific product to the cart with a value selected. Confirm the
      value appears under the line item in the cart and at checkout review.
- [ ] Complete a test order. Confirm the field label/value appears on the
      order-received page, the admin Order screen (line item meta), and the
      "New Order" email.
- [ ] Create a **Checkbox Group** field with multiple selections; confirm all
      selected values are stored and displayed (comma-separated).
- [ ] Deactivate and reactivate the plugin; confirm existing fields and their
      settings are unaffected.

## Roadmap

1. ✅ Product Fields (this phase)
2. Conditional display engine (other-field-value, category/attribute conditions)
3. Pricing (fees/discounts) + live AJAX price updates
4. File upload field type + secure storage/downloads
5. Checkout Fields, Order Fields (admin-editable), Customer Fields
6. Date/time/color field types, quantity-based repeated fields
7. Remaining condition types, third-party plugin integrations

## Architecture notes

- Field definitions are stored as a non-public custom post type (`wfb_field`)
  with settings in postmeta — no custom DB tables.
- Field types are pluggable: implement `WooFieldBuilder\Fields\FieldTypeInterface`
  and register it via the `wfb_register_field_type` action.
- No code in this project was copied from any third-party plugin; it was
  written from scratch against WordPress/WooCommerce core APIs.
