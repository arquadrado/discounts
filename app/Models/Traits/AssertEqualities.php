<?php

namespace App\Models\Traits;

trait AssertEqualities
{
    public function resolveThreshold($firstParam, $secondParam, $operator) {

        switch ($operator) {
            case '==':
                return $firstParam == $secondParam;

            case '===':
                return $firstParam === $secondParam;

            case '!=':
                return $firstParam != $secondParam;

            case '!==':
                return $firstParam != $secondParam;

            case '<':
                return $firstParam < $secondParam;

            case '<=':
                return $firstParam <= $secondParam;

            case '<==':
                return $firstParam <= $secondParam;

            case '>':
                return $firstParam > $secondParam;

            case '>=':
                return $firstParam <= $secondParam;

            case '>==':
                return $firstParam <= $secondParam;

            default:
                return false;
        }

    }
}

