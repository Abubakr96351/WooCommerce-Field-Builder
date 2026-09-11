<?php

namespace WooFieldBuilder\Fields\Types;

use WooFieldBuilder\Fields\AbstractMultiChoiceFieldType;

if (!defined('ABSPATH')) {
    exit;
}

class CheckboxGroupFieldType extends AbstractMultiChoiceFieldType
{
    public function get_slug(): string
    {
        return 'checkbox_group';
    }

    public function get_label(): string
    {
        return __('Checkbox Group', 'woo-field-builder');
    }

    public function render_frontend(array $field, $value): void
    {
        $options  = $field['options'] ?? [];
        $selected = is_array($value) ? $value : [];
        ?>
        <fieldset class="form-row wfb-field wfb-field-checkbox-group">
            <legend>
                <?php echo esc_html($field['label']); ?>
                <?php if (!empty($field['required'])) : ?><span class="required">*</span><?php endif; ?>
            </legend>
            <?php foreach ($options as $index => $option) : ?>
                <label for="<?php echo esc_attr($field['key'] . '_' . $index); ?>">
                    <input
                        type="checkbox"
                        name="<?php echo esc_attr($field['key']); ?>[]"
                        id="<?php echo esc_attr($field['key'] . '_' . $index); ?>"
                        value="<?php echo esc_attr($option['key']); ?>"
                        <?php checked(in_array($option['key'], $selected, true)); ?>
                    />
                    <?php echo esc_html($option['label']); ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
        <?php
    }
}
