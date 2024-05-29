<?php

namespace app\components;

trait BaseEnum
{
    public function label(): string
    {
        return static::getLabel($this);
    }

    public static function getLabel(self $value): ?string
    {
        $labels = [];
        foreach (static::cases() as $case) {
            $labels[$case->name] = $case->name;
        }

        return $labels[$value->name] ?? null;
    }

    public static function getValues(): array
    {
        $values = [];
        foreach (static::cases() as $case) {
            $values[] = $case->value ?? $case->name;
        }

        return $values;
    }
}
