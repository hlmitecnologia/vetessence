<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
{
    public function passes($attribute, $value)
    {
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($cpf) != 11) {
            return false;
        }
        
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            $c = 0;
            for ($i = 0; $i < $t; $i++) {
                $c += $cpf[$i] * (($t + 1) - $i);
            }
            $d = (($c * 10) % 11) % 10;
            if ($cpf[$i] != $d) {
                return false;
            }
        }
        
        return true;
    }

    public function message()
    {
        return 'O CPF informado é inválido.';
    }
}
