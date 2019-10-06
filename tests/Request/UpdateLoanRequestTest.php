<?php

namespace App\Tests\Requests;

use App\Request\UpdateLoanRequest;
use App\Tests\Mothers\LoanMother;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ramsey\Uuid\Uuid;

class UpdateLoanRequestTest extends TestCase
{
    protected function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_construct(): void
    {
        $loan = LoanMother::random()->jsonSerialize();
        $request = new UpdateLoanRequest($loan['id'], $loan['itemId'], $loan['loaner'], $loan['loanDate'], $loan['returnDate']);

        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($request);

        $this->assertCount(0, $errors);
        $this->assertSame($loan['id'], $request->getId()->toString());
        $this->assertSame($loan['itemId'], $request->getItemId()->toString());
        $this->assertSame($loan['loaner'], $request->getLoaner());
        $this->assertSame($loan['loanDate'], $request->getLoanDate());
        $this->assertSame($loan['returnDate'], $request->getReturnDate());
    }
}