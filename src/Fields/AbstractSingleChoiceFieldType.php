<?php

namespace WooFieldBuilder\Fields;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared logic for field types that store a single value chosen from a
 * configured options list (select, radio group).
 */
abstract class AbstractSingleChoiceFieldType extends AbstractFieldType
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
        return sanitize_text_field(wp_unslash((string) $raw_value));
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

        if (!in_array((string) $raw_value, $valid_keys, true)) {
            return sprintf(
                /* translators: %s: field label */
                __('%s has an invalid selection.', 'woo-field-builder'),
                $field['label']
            );
        }

        return null;
    }

    public function format_value_for_display($value, array $field): string
    {
        return implode(', ', $this->map_options_to_labels($value, $field));
    }
}
