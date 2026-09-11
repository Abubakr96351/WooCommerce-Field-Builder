<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class TextareaFieldType extends AbstractFieldType
{
    public function get_slug(): string
    {
        return 'textarea';
    }

    public function get_label(): string
    {
        return __('Textarea', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        printf(
            '<p class="form-row wfb-field wfb-field-textarea">
                <label for="%1$s">%2$s%3$s</label>
                <textarea class="input-text" name="%1$s" id="%1$s" placeholder="%4$s" rows="4" %5$s>%6$s</textarea>
            </p>',
            esc_attr($field['key']),
            esc_html($field['label']),
            !empty($field['required']) ? ' <span class="required">*</span>' : '',
            esc_attr($field['placeholder'] ?? ''),
            !empty($field['required']) ? 'required' : '',
            esc_textarea((string) $value)
        );
    }

    public function sanitize($raw_value)
    {
        return sanitize_textarea_field(wp_unslash((string) $raw_value));
    }
}
