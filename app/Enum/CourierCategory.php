<?php

namespace App\Enum;

enum CourierCategory: string
{
    case CATEGORY_1 = 'category_1';
    case CATEGORY_2 = 'category_2';
    case CATEGORY_3 = 'category_3';

    /**
     * The most restrictive category across a set of categories is the one with
     * the highest capability (a bigger vehicle can carry everything a smaller
     * vehicle can, so the courier must support the highest required category).
     *
     * @param  array<int, string>  $categories
     */
    public static function mostRestrictive(array $categories): ?self
    {
        $rank = [
            self::CATEGORY_3->value => 3,
            self::CATEGORY_2->value => 2,
            self::CATEGORY_1->value => 1,
        ];

        $highest = null;
        $highestRank = 0;

        foreach ($categories as $category) {
            $value = (string) $category;
            if (! isset($rank[$value])) {
                continue;
            }

            if ($rank[$value] > $highestRank) {
                $highestRank = $rank[$value];
                $highest = self::from($value);
            }
        }

        return $highest;
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $category) => $category->value,
            self::cases()
        );
    }
}
