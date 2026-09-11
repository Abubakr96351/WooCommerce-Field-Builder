<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class TextFieldType extends AbstractFieldType
{
    public function get_slug(): string
    {
        return 'text';
    }

    public function get_label(): string
    {
        return __('Text', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        printf(
            '<p class="form-row wfb-field wfb-field-text">
                <label for="%1$s">%2$s%3$s</label>
                <input type="text" class="input-text" name="%1$s" id="%1$s" placeholder="%4$s" value="%5$s" %6$s />
            </p>',
            esc_attr($field['key']),
            esc_html($field['label']),
            !empty($field['required']) ? ' <span class="required">*</span>' : '',
            esc_attr($field['placeholder'] ?? ''),
            esc_attr((string) $value),
            !empty($field['required']) ? 'required' : ''
        );
    }

    public function sanitize($raw_value)
    {
        return sanitize_text_field(wp_unslash((string) $raw_value));
    }
}
