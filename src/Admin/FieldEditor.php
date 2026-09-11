<?php

namespace WooFieldBuilder\Admin;

use WooFieldBuilder\Fields\FieldTypeRegistry;
use WooFieldBuilder\PostTypes\FieldPostType;
use WooFieldBuilder\Support\FieldRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Meta boxes for creating/editing a `wfb_field` post: type, key, required,
 * placeholder, type-specific options, and product scope.
 */
class FieldEditor
{
    private const NONCE_ACTION = 'wfb_save_field';
    private const NONCE_NAME   = 'wfb_field_nonce';

    private FieldTypeRegistry $field_type_registry;
    private FieldRepository $field_repository;

    public function __construct(FieldTypeRegistry $field_type_registry, FieldRepository $field_repository)
    {
        $this->field_type_registry = $field_type_registry;
        $this->field_repository    = $field_repository;
    }

    public function register_hooks(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . FieldPostType::POST_TYPE, [$this, 'save_field'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(string $hook): void
    {
        global $post_type;

        if (FieldPostType::POST_TYPE !== $post_type) {
            return;
        }

        wp_enqueue_style('woocommerce_admin_styles');
        wp_enqueue_script('wc-enhanced-select');

        wp_enqueue_style('wfb-admin', WFB_PLUGIN_URL . 'assets/css/admin.css', [], WFB_VERSION);
        wp_enqueue_script('wfb-admin', WFB_PLUGIN_URL . 'assets/js/admin.js', ['jquery', 'wc-enhanced-select'], WFB_VERSION, true);
    }

    public function add_meta_boxes(): void
    {
        add_meta_box(
            'wfb-field-settings',
            __('Field Settings', 'woo-field-builder'),
            [$this, 'render_settings_meta_box'],
            FieldPostType::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'wfb-field-scope',
            __('Apply To', 'woo-field-builder'),
            [$this, 'render_scope_meta_box'],
            FieldPostType::POST_TYPE,
            'side',
            'default'
        );
    }

    public function render_settings_meta_box(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $field = $this->field_repository->get_field($post->ID) ?? [
            'type'        => 'text',
            'key'         => '',
            'required'    => false,
            'placeholder' => '',
            'options'     => [],
        ];

        $type_choices = $this->field_type_registry->get_choices();
        ?>
        <table class="form-table">
            <tr>
                <th><label for="wfb_type"><?php esc_html_e('Field Type', 'woo-field-builder'); ?></label></th>
                <td>
                    <select name="wfb_type" id="wfb_type">
                        <?php foreach ($type_choices as $slug => $label) : ?>
                            <option value="<?php echo esc_attr($slug); ?>" <?php selected($field['type'], $slug); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="wfb_key"><?php esc_html_e('Field Key', 'woo-field-builder'); ?></label></th>
                <td>
                    <input type="text" name="wfb_key" id="wfb_key" class="regular-text" value="<?php echo esc_attr($field['key']); ?>" placeholder="<?php esc_attr_e('e.g. gift_message', 'woo-field-builder'); ?>" />
                    <p class="description"><?php esc_html_e('Unique machine name (letters, numbers, underscores). Used internally to store the value.', 'woo-field-builder'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wfb_placeholder"><?php esc_html_e('Placeholder', 'woo-field-builder'); ?></label></th>
                <td>
                    <input type="text" name="wfb_placeholder" id="wfb_placeholder" class="regular-text" value="<?php echo esc_attr($field['placeholder']); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="wfb_required"><?php esc_html_e('Required', 'woo-field-builder'); ?></label></th>
                <td>
                    <input type="checkbox" name="wfb_required" id="wfb_required" value="1" <?php checked($field['required']); ?> />
                </td>
            </tr>
        </table>

        <?php foreach ($this->field_type_registry->all() as $slug => $type) : ?>
            <div class="wfb-type-settings" data-type="<?php echo esc_attr($slug); ?>" style="<?php echo $field['type'] === $slug ? '' : 'display:none;'; ?>">
                <?php $type->render_admin_settings($field); ?>
            </div>
        <?php endforeach; ?>
        <?php
    }

    public function render_scope_meta_box(\WP_Post $post): void
    {
        $field       = $this->field_repository->get_field($post->ID);
        $scope       = $field['scope'] ?? 'all';
        $product_ids = $field['product_ids'] ?? [];
        ?>
        <p>
            <label>
                <input type="radio" name="wfb_scope" value="all" <?php checked($scope, 'all'); ?> />
                <?php esc_html_e('All products', 'woo-field-builder'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="wfb_scope" value="specific" <?php checked($scope, 'specific'); ?> />
                <?php esc_html_e('Specific products', 'woo-field-builder'); ?>
            </label>
        </p>
        <p class="wfb-specific-products" style="<?php echo 'specific' === $scope ? '' : 'display:none;'; ?>">
            <select
                class="wc-product-search"
                name="wfb_product_ids[]"
                multiple="multiple"
                style="width: 100%;"
                data-action="woocommerce_json_search_products_and_variations"
                data-placeholder="<?php esc_attr_e('Search for products...', 'woo-field-builder'); ?>"
            >
                <?php foreach ($product_ids as $product_id) : ?>
                    <?php $product = wc_get_product($product_id); ?>
                    <?php if ($product) : ?>
                        <option value="<?php echo esc_attr($product_id); ?>" selected="selected">
                            <?php echo esc_html($product->get_formatted_name()); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * @param int      $post_id
     * @param \WP_Post $post
     */
    public function save_field(int $post_id, \WP_Post $post): void
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce(wp_unslash($_POST[self::NONCE_NAME]), self::NONCE_ACTION)) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }

        $type = isset($_POST['wfb_type']) ? sanitize_key(wp_unslash($_POST['wfb_type'])) : 'text';

        if (!$this->field_type_registry->has($type)) {
            $type = 'text';
        }

        $key = isset($_POST['wfb_key']) ? sanitize_key(wp_unslash($_POST['wfb_key'])) : '';

        update_post_meta($post_id, '_wfb_type', $type);
        update_post_meta($post_id, '_wfb_key', $key);
        update_post_meta($post_id, '_wfb_required', isset($_POST['wfb_required']) ? 1 : 0);
        update_post_meta($post_id, '_wfb_placeholder', isset($_POST['wfb_placeholder']) ? sanitize_text_field(wp_unslash($_POST['wfb_placeholder'])) : '');

        $options = [];

        if (isset($_POST['wfb_option_key'], $_POST['wfb_option_label']) && is_array($_POST['wfb_option_key'])) {
            $keys   = wp_unslash($_POST['wfb_option_key']);
            $labels = wp_unslash($_POST['wfb_option_label']);

            foreach ($keys as $index => $option_key) {
                $option_key = sanitize_text_field($option_key);

                if ('' === $option_key) {
                    continue;
                }

                $options[] = [
                    'key'   => $option_key,
                    'label' => isset($labels[$index]) ? sanitize_text_field($labels[$index]) : $option_key,
                ];
            }
        }

        update_post_meta($post_id, '_wfb_options', $options);

        $scope = isset($_POST['wfb_scope']) && 'specific' === $_POST['wfb_scope'] ? 'specific' : 'all';
        update_post_meta($post_id, '_wfb_scope', $scope);

        $product_ids = [];

        if (isset($_POST['wfb_product_ids']) && is_array($_POST['wfb_product_ids'])) {
            $product_ids = array_map('absint', wp_unslash($_POST['wfb_product_ids']));
        }

        // Stored as individual repeated meta rows (not one serialized array)
        // so `_wfb_product_ids` can be queried with an exact meta_query match
        // instead of a LIKE against serialized data, which can false-match
        // on the array's internal index rather than its value.
        delete_post_meta($post_id, '_wfb_product_ids');

        foreach (array_unique($product_ids) as $product_id) {
            add_post_meta($post_id, '_wfb_product_ids', $product_id);
        }
    }
}
