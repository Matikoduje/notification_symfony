<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute] class Isbn extends Constraint
{
    public string $message = 'The ISBN "{{ value }}" is not a valid ISBN number.';
}
