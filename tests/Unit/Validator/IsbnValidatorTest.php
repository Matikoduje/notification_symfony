<?php

namespace App\Tests\Unit\Validator;

use App\Validator\Isbn;
use App\Validator\IsbnValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class IsbnValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new IsbnValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Isbn());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new Isbn());

        $this->assertNoViolation();
    }

    public function testValidIsbn10()
    {
        $this->validator->validate('048665088X', new Isbn());

        $this->assertNoViolation();
    }

    public function testInvalidIsbn10()
    {
        $this->validator->validate('0486650889', new Isbn());

        $this->buildViolation('The ISBN "{{ value }}" is not a valid ISBN number.')
            ->setParameter('{{ value }}', '0486650889')
            ->assertRaised();
    }

    public function testValidIsbn13()
    {
        $this->validator->validate('9783161484100', new Isbn());

        $this->assertNoViolation();
    }

    public function testInvalidIsbn13()
    {
        $this->validator->validate('9783161484101', new Isbn());

        $this->buildViolation('The ISBN "{{ value }}" is not a valid ISBN number.')
            ->setParameter('{{ value }}', '9783161484101')
            ->assertRaised();
    }
}
