<?php

namespace App\Models\Traits;

trait AssertEqualities
{
    public function shouldTrigger($firstParam, $secondParam, $operator) {

        switch ($operator) {
            case '==':
                return $firstParam == $secondParam;

            case '!=':
                return $firstParam != $secondParam;

            case '<':
                return $firstParam < $secondParam;

            case '<=':
                return $firstParam <= $secondParam;

            case '>':
                return $firstParam > $secondParam;

            case '>=':
                return $firstParam <= $secondParam;


            default:
                return false;
        }

    }
}

