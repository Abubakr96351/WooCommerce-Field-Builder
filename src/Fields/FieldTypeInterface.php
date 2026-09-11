<?php

namespace WooFieldBuilder\Fields;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contract every field type (text, select, checkbox group, etc.) must implement.
 *
 * A "$field" array always has at least: id, key, type, label, required, placeholder, options.
 */
interface FieldTypeInterface
{
    /**
     * Unique machine name for this field type, e.g. "text".
     */
    public function get_slug(): string;

    /**
     * Human readable label shown in the admin "field type" dropdown.
     */
    public function get_label(): string;

    /**
     * Whether this field type uses the options list (select/multiselect/checkbox/radio).
     */
    public function has_options(): bool;

    /**
     * Output any type-specific admin settings inputs (beyond the common ones
     * handled by FieldEditor), e.g. an options repeater.
     *
     * @param array $field Current field data (may be empty for a new field).
     */
    public function render_admin_settings(array $field): void;

    /**
     * Output the actual frontend form input(s) for this field.
     *
     * @param array $field Field definition.
     * @param mixed $value Current/submitted value, if any.
     */
    public function render_frontend(array $field, $value): void;

    /**
     * Sanitize a raw submitted value before it is stored.
     *
     * @param mixed $raw_value
     * @return mixed
     */
    public function sanitize($raw_value);

    /**
     * Validate a raw submitted value against the field definition.
     *
     * @param mixed $raw_value
     * @param array $field
     * @return string|null Error message, or null if valid.
     */
    public function validate($raw_value, array $field): ?string;

    /**
     * Format a stored value for human-readable display (cart, order, emails).
     *
     * @param mixed $value
     * @param array $field
     */
    public function format_value_for_display($value, array $field): string;
}
