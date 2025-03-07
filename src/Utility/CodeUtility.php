<?php

namespace App\Utility;

use Cake\Log\Log;
use Random\RandomException;

class CodeUtility
{
    /**
     * Generate Code - produces a variable length random Alphanumeric String
     *
     * @param int $length
     * @return string
     */
    public static function generateCode(int $length = 5): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $maxIndex = strlen($characters) - 1;
        $code = '';

        try {
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $maxIndex)];
            }
        } catch (RandomException) {
            Log::error('Random exception');
        }

        return $code;
    }
}
