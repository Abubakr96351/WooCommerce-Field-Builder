<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractMultiChoiceFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class MultiSelectFieldType extends AbstractMultiChoiceFieldType
{
    public function get_slug(): string
    {
        return 'multiselect';
    }

    public function get_label(): string
    {
        return __('Multi-Select', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        $options  = $field['options'] ?? [];
        $selected = is_array($value) ? $value : [];
        ?>
        <p class="form-row wfb-field wfb-field-multiselect">
            <label for="<?php echo esc_attr($field['key']); ?>">
                <?php echo esc_html($field['label']); ?>
                <?php if (!empty($field['required'])) : ?><span class="required">*</span><?php endif; ?>
            </label>
            <select class="wfb-select" name="<?php echo esc_attr($field['key']); ?>[]" id="<?php echo esc_attr($field['key']); ?>" multiple="multiple" <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                <?php foreach ($options as $option) : ?>
                    <option value="<?php echo esc_attr($option['key']); ?>" <?php selected(in_array($option['key'], $selected, true)); ?>>
                        <?php echo esc_html($option['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
}
