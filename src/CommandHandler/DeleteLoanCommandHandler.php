<?php

namespace App\CommandHandler;

use App\Command\DeleteLoanCommand;
//use App\Entity\Category;
use App\Repository\LoanRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\LoanNotDeletedException;
use Psr\Log\LoggerInterface;

class DeleteLoanCommandHandler implements CommandHandlerInterface
{
    /**
     * @var LoanRepository
     */
    private $repository;    
    /**
     * @var MessageBusInterface
     */
    private $eventBus;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param MessageBusInterface $eventBus
     * @param LoanRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, LoanRepository $repository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param DeleteLoanCommand $command
     */
    public function __invoke(DeleteLoanCommand $command)
    {
        try {
            $loan = $this->repository->getLoan($command->getId());
            $this->repository->delete($loan);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LoanNotDeletedException('Loan was not deleted: '.$e->getMessage());
        }
    }
}
