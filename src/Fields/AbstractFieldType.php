<?php

namespace WooFieldBuilder\Fields;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared behaviour for field types: required-field validation and the
 * options-repeater admin UI used by select/multiselect/checkbox/radio types.
 */
abstract class AbstractFieldType implements FieldTypeInterface
{
    public function has_options(): bool
    {
        return false;
    }

    public function render_admin_settings(array $field): void
    {
        // Most field types have no extra settings beyond the common ones.
    }

    public function validate($raw_value, array $field): ?string
    {
        if (!empty($field['required']) && $this->is_empty($raw_value)) {
            return sprintf(
                /* translators: %s: field label */
                __('%s is required.', 'woo-field-builder'),
                $field['label']
            );
        }

        return null;
    }

    public function format_value_for_display($value, array $field): string
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return (string) $value;
    }

    protected function is_empty($value): bool
    {
        if (is_array($value)) {
            return empty($value);
        }

        return '' === trim((string) $value);
    }

    /**
     * Renders the shared "options" repeater table used by choice-based field
     * types. Field markup uses name="wfb_options[]" rows with key/label pairs.
     *
     * @param array $field
     */
    protected function render_options_admin_settings(array $field): void
    {
        $options = $field['options'] ?? [];

        include WFB_PLUGIN_DIR . 'src/Admin/views/field-options-repeater.php';
    }

    /**
     * Maps stored option key(s) to their configured label(s) for display.
     *
     * @param mixed $value
     * @param array $field
     */
    protected function map_options_to_labels($value, array $field): array
    {
        $options = $field['options'] ?? [];
        $map     = [];

        foreach ($options as $option) {
            $map[$option['key']] = $option['label'];
        }

        $values = is_array($value) ? $value : [$value];
        $labels = [];

        foreach ($values as $single) {
            $labels[] = $map[$single] ?? $single;
        }

        return $labels;
    }
}
