<?php

namespace App\Test\Command;

use App\Command\UpdateLoanCommand;
use App\Tests\Mothers\LoanMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateLoanCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $loan = LoanMother::random()->jsonSerialize();
        $command = new UpdateLoanCommand(
            Uuid::fromString($loan['id']), Uuid::fromString($loan['itemId']), $loan['loaner'], $loan['loanDate'], $loan['returnDate']);
        $this->assertSame($loan['id'], $command->getId()->toString());
        $this->assertSame($loan['itemId'], $command->getItemId()->toString());
        $this->assertSame($loan['loaner'], $command->getLoaner());
        $this->assertSame($loan['loanDate'], $command->getLoanDate());
        $this->assertSame($loan['returnDate'], $command->getReturnDate());
    }
}