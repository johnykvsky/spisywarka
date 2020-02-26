<?php

namespace App\Tests\CommandHandler;

use App\Command\CreateLoanCommand;
use App\CommandHandler\CreateLoanCommandHandler;
use App\CommandHandler\Exception\LoanNotCreatedException;
use App\Entity\Loan;
use App\Repository\LoanRepository;
use App\Repository\ItemRepository;
use App\Tests\Mothers\LoanMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class CreateLoanCommandHandlerTest extends TestCase
{
    /**
     * @throws LoanNotCreatedException
     * @throws \Assert\AssertionFailedException
     */
    public function test_loan_created(): void
    {
        $loanMock = LoanMother::random();

        $repository = $this->createMock(LoanRepository::class);
        $itemRepository = $this->createMock(ItemRepository::class);
        $itemRepository->method('getItem')->willReturn($loanMock->getItem());

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (Loan $loan) use ($loanMock) {
                        self::assertSame($loanMock->getId(), $loan->getId());
                        self::assertSame($loanMock->getItem()->getId()->toString(), $loan->getItem()->getId()->toString());
                        self::assertSame($loanMock->getLoaner(), $loan->getLoaner());
                        return true;
                    }
                )
            );

        $command = new CreateLoanCommand(
            $loanMock->getId(), 
            $loanMock->getItem()->getId(), 
            $loanMock->getLoaner(), 
            $loanMock->getLoanDate(),
            $loanMock->getReturnDate()
        );
        
        $logger = $this->createMock(LoggerInterface::class);
        $eventBus = $this->createMock(MessageBusInterface::class);
        
        $handler = new CreateLoanCommandHandler($eventBus, $repository, $logger, $itemRepository);
        
        $handler($command);
    }
}