<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ClosedPolygonRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('Поле :attribute должно быть массивом точек.');
            return;
        }

        if (count($value) < 4) {
            $fail('В поле :attribute минимально должно быть 4 точки');
            return;
        }

        $first = trim((string)$value[0]);
        $last  = trim((string)$value[count($value) - 1]);

        if ($first !== $last) {
            $fail('Первый и последний элементы должны совпадать (замкнутый контур).');
        }
    }
}
