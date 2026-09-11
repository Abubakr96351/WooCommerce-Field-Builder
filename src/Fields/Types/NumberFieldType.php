<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class NumberFieldType extends AbstractFieldType
{
    public function get_slug(): string
    {
        return 'number';
    }

    public function get_label(): string
    {
        return __('Number', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        printf(
            '<p class="form-row wfb-field wfb-field-number">
                <label for="%1$s">%2$s%3$s</label>
                <input type="number" step="1" class="input-text" name="%1$s" id="%1$s" placeholder="%4$s" value="%5$s" %6$s />
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
        $raw_value = wp_unslash((string) $raw_value);

        return is_numeric($raw_value) ? $raw_value + 0 : '';
    }

    public function validate($raw_value, array $field): ?string
    {
        $error = parent::validate($raw_value, $field);

        if (null !== $error) {
            return $error;
        }

        if (!$this->is_empty($raw_value) && !is_numeric(wp_unslash((string) $raw_value))) {
            return sprintf(
                /* translators: %s: field label */
                __('%s must be a number.', 'woo-field-builder'),
                $field['label']
            );
        }

        return null;
    }
}
