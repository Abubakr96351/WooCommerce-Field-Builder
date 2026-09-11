<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractSingleChoiceFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class RadioGroupFieldType extends AbstractSingleChoiceFieldType
{
    public function get_slug(): string
    {
        return 'radio_group';
    }

    public function get_label(): string
    {
        return __('Radio Group', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        $options = $field['options'] ?? [];
        ?>
        <fieldset class="form-row wfb-field wfb-field-radio-group">
            <legend>
                <?php echo esc_html($field['label']); ?>
                <?php if (!empty($field['required'])) : ?><span class="required">*</span><?php endif; ?>
            </legend>
            <?php foreach ($options as $index => $option) : ?>
                <label for="<?php echo esc_attr($field['key'] . '_' . $index); ?>">
                    <input
                        type="radio"
                        name="<?php echo esc_attr($field['key']); ?>"
                        id="<?php echo esc_attr($field['key'] . '_' . $index); ?>"
                        value="<?php echo esc_attr($option['key']); ?>"
                        <?php checked($value, $option['key']); ?>
                        <?php echo !empty($field['required']) ? 'required' : ''; ?>
                    />
                    <?php echo esc_html($option['label']); ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
        <?php
    }
}
