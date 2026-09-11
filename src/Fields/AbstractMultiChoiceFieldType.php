<?php

namespace WooFieldBuilder\Fields;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared logic for field types that store multiple values chosen from a
 * configured options list (multi-select, checkbox group).
 */
abstract class AbstractMultiChoiceFieldType extends AbstractFieldType
{
    public function has_options(): bool
    {
        return true;
    }

    public function render_admin_settings(array $field): void
    {
        $this->render_options_admin_settings($field);
    }

    public function sanitize($raw_value)
    {
        if (!is_array($raw_value)) {
            return [];
        }

        return array_map('sanitize_text_field', wp_unslash($raw_value));
    }

    public function validate($raw_value, array $field): ?string
    {
        $error = parent::validate($raw_value, $field);

        if (null !== $error) {
            return $error;
        }

        if ($this->is_empty($raw_value)) {
            return null;
        }

        $valid_keys = wp_list_pluck($field['options'] ?? [], 'key');
        $values     = is_array($raw_value) ? $raw_value : [$raw_value];

        foreach ($values as $single) {
            if (!in_array((string) $single, $valid_keys, true)) {
                return sprintf(
                    /* translators: %s: field label */
                    __('%s has an invalid selection.', 'woo-field-builder'),
                    $field['label']
                );
            }
        }

        return null;
    }

    public function format_value_for_display($value, array $field): string
    {
        return implode(', ', $this->map_options_to_labels($value, $field));
    }
}
