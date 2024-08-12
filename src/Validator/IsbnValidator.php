<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsbnValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint Isbn */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->isValidIsbn($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValidIsbn(string $isbn): bool
    {
        $isbn = str_replace(['-', ' '], '', $isbn);

        if (preg_match('/^\d{9}[\d|X]$/', $isbn) === 1) {
            return $this->isValidIsbn10($isbn);
        }

        if (preg_match('/^\d{13}$/', $isbn)) {
            return $this->isValidIsbn13($isbn);
        }

        return false;
    }

    private function isValidIsbn10(string $isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $isbn[$i]) * ($i + 1);
        }

        $checksum = $isbn[9];
        $checksum = ($checksum === 'X') ? 10 : (int) $checksum;

        return ($sum % 11) === $checksum;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $sum += ((int) $isbn[$i]) * ($i % 2 === 0 ? 1 : 3);
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int) $isbn[12];
    }
}
