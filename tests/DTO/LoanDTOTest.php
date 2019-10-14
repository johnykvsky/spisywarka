<?php

namespace App\Test\Command;

use App\DTO\LoanDTO;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Faker\Factory;

class LoanDTOTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $id = Uuid::uuid4();
        $itemId = Uuid::uuid4();
        $loaner = $faker->firstName();
        $loanDate = new \DateTime;

        $dto = new LoanDTO($id->toString(), $itemId->toString(), $loaner, $loanDate, null);

        $this->assertSame($id->toString(), $dto->getId()->toString());
        $this->assertSame($itemId->toString(), $dto->getItemId()->toString());
        $this->assertSame($loaner, $dto->getLoaner());
        $this->assertSame($loanDate, $dto->getLoanDate());
        $this->assertSame(null, $dto->getReturnDate());
    }
}