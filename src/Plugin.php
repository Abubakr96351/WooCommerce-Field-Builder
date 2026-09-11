<?php

namespace WooFieldBuilder;

use WooFieldBuilder\PostTypes\FieldPostType;
use WooFieldBuilder\Admin\FieldEditor;
use WooFieldBuilder\Fields\FieldTypeRegistry;
use WooFieldBuilder\Fields\Types\TextFieldType;
use WooFieldBuilder\Fields\Types\TextareaFieldType;
use WooFieldBuilder\Fields\Types\NumberFieldType;
use WooFieldBuilder\Fields\Types\SelectFieldType;
use WooFieldBuilder\Fields\Types\MultiSelectFieldType;
use WooFieldBuilder\Fields\Types\CheckboxGroupFieldType;
use WooFieldBuilder\Fields\Types\RadioGroupFieldType;
use WooFieldBuilder\Support\FieldRepository;
use WooFieldBuilder\Frontend\ProductFieldRenderer;
use WooFieldBuilder\Frontend\CartHandler;
use WooFieldBuilder\Frontend\OrderHandler;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Singleton bootstrap: wires up all plugin components.
 */
class Plugin
{
    private static ?Plugin $instance = null;

    private FieldTypeRegistry $field_type_registry;
    private FieldRepository $field_repository;

    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function boot(): void
    {
        $this->field_type_registry = new FieldTypeRegistry();
        $this->register_field_types();

        $this->field_repository = new FieldRepository($this->field_type_registry);

        (new FieldPostType())->register_hooks();
        (new FieldEditor($this->field_type_registry, $this->field_repository))->register_hooks();

        (new ProductFieldRenderer($this->field_repository))->register_hooks();
        (new CartHandler($this->field_repository))->register_hooks();
        (new OrderHandler())->register_hooks();
    }

    private function register_field_types(): void
    {
        $this->field_type_registry->register(new TextFieldType());
        $this->field_type_registry->register(new TextareaFieldType());
        $this->field_type_registry->register(new NumberFieldType());
        $this->field_type_registry->register(new SelectFieldType());
        $this->field_type_registry->register(new MultiSelectFieldType());
        $this->field_type_registry->register(new CheckboxGroupFieldType());
        $this->field_type_registry->register(new RadioGroupFieldType());

        /**
         * Fires after built-in field types are registered, so third parties
         * (or later phases of this plugin) can register additional types.
         *
         * @param FieldTypeRegistry $registry
         */
        do_action('wfb_register_field_type', $this->field_type_registry);
    }

    public function get_field_type_registry(): FieldTypeRegistry
    {
        return $this->field_type_registry;
    }

    public function get_field_repository(): FieldRepository
    {
        return $this->field_repository;
    }
}
