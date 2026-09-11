<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractSingleChoiceFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class SelectFieldType extends AbstractSingleChoiceFieldType
{
    public function get_slug(): string
    {
        return 'select';
    }

    public function get_label(): string
    {
        return __('Select', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        $options = $field['options'] ?? [];
        ?>
        <p class="form-row wfb-field wfb-field-select">
            <label for="<?php echo esc_attr($field['key']); ?>">
                <?php echo esc_html($field['label']); ?>
                <?php if (!empty($field['required'])) : ?><span class="required">*</span><?php endif; ?>
            </label>
            <select class="wfb-select" name="<?php echo esc_attr($field['key']); ?>" id="<?php echo esc_attr($field['key']); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                <option value=""><?php esc_html_e('-- Choose an option --', 'woo-field-builder'); ?></option>
                <?php foreach ($options as $option) : ?>
                    <option value="<?php echo esc_attr($option['key']); ?>" <?php selected($value, $option['key']); ?>>
                        <?php echo esc_html($option['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
}
