<?php

namespace App\Tests\CommandHandler;

use App\Command\DeleteLoanCommand;
use App\CommandHandler\DeleteLoanCommandHandler;
use App\CommandHandler\Exception\LoanNotDeletedException;
use App\Entity\Loan;
use App\Repository\Exception\LoanNotFoundException;
use App\Repository\LoanRepository;
use App\Tests\Mothers\LoanMother;
use Assert\AssertionFailedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Tests\Fixtures\ChildDummyMessage;
use Psr\Log\LoggerInterface;

Class DeleteLoanCommandHandlerTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function test_delete_loan(): void
    {
        $loanMock = LoanMother::random();
        $id = $loanMock->getId();

        $repository = $this->createMock(LoanRepository::class);
        $repository->method('getLoan')->with($id)->willReturn($loanMock);
        $repository->expects(self::once())
            ->method('delete')
            ->with(self::callback(
                static function (Loan $loan) use ($id) {
                    self::assertSame($id, $loan->getId());
                    return true;
                }
            ));

        $eventBus = $this->createMock(MessageBusInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $command = new DeleteLoanCommand($id);
        $handler = new DeleteLoanCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }

    /**
     * @throws LoanNotFoundException
     * @throws LoanNotDeletedException
     * @throws Exception
     */
    public function test_throws_LoanNotFoundException_when_invalid_uuid(): void
    {
        $this->expectException(LoanNotDeletedException::class);

        $id = Uuid::uuid4();
        $repository = $this->createMock(LoanRepository::class);
        $repository->method('getLoan')->with($id)->willThrowException(new LoanNotFoundException());
        $eventBus = $this->createMock(MessageBusInterface::class);
        $command = new DeleteLoanCommand($id);
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new DeleteLoanCommandHandler($eventBus, $repository, $logger);
        $handler($command);
    }
}