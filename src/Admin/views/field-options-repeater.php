<?php
/**
 * Options repeater UI, included by AbstractFieldType::render_options_admin_settings().
 *
 * Expects $options to be an array of ['key' => ..., 'label' => ...].
 *
 * @var array $options
 */

if (!defined('ABSPATH')) {
    exit;
}

if (empty($options)) {
    $options = [['key' => '', 'label' => '']];
}
?>
<table class="wfb-options-repeater widefat" style="max-width: 480px;">
    <thead>
        <tr>
            <th><?php esc_html_e('Value', 'woo-field-builder'); ?></th>
            <th><?php esc_html_e('Label', 'woo-field-builder'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($options as $option) : ?>
        <tr class="wfb-options-repeater-row">
            <td>
                <input type="text" name="wfb_option_key[]" value="<?php echo esc_attr($option['key']); ?>" class="regular-text" />
            </td>
            <td>
                <input type="text" name="wfb_option_label[]" value="<?php echo esc_attr($option['label']); ?>" class="regular-text" />
            </td>
            <td>
                <button type="button" class="button wfb-remove-option"><?php esc_html_e('Remove', 'woo-field-builder'); ?></button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p>
    <button type="button" class="button wfb-add-option"><?php esc_html_e('Add Option', 'woo-field-builder'); ?></button>
</p>
