<?php

namespace WooFieldBuilder\Fields;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Holds all registered field types, keyed by slug.
 */
class FieldTypeRegistry
{
    /** @var array<string, FieldTypeInterface> */
    private array $types = [];

    public function register(FieldTypeInterface $type): void
    {
        $this->types[$type->get_slug()] = $type;
    }

    public function get(string $slug): ?FieldTypeInterface
    {
        return $this->types[$slug] ?? null;
    }

    public function has(string $slug): bool
    {
        return isset($this->types[$slug]);
    }

    /**
     * @return array<string, FieldTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * @return array<string, string> slug => label, for admin dropdowns.
     */
    public function get_choices(): array
    {
        $choices = [];

        foreach ($this->types as $slug => $type) {
            $choices[$slug] = $type->get_label();
        }

        return $choices;
    }
}
