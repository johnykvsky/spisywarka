<?php

namespace App\CommandHandler;

use App\Command\CreateLoanCommand;
use App\Entity\Loan;
use App\Repository\LoanRepository;
use App\Repository\ItemRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\LoanNotCreatedException;
use Psr\Log\LoggerInterface;

class CreateLoanCommandHandler implements CommandHandlerInterface
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
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @param MessageBusInterface $eventBus
     * @param LoanRepository $repository
     * @param LoggerInterface $logger
     * @param ItemRepository $itemRepository
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        LoanRepository $repository, 
        LoggerInterface $logger,
        ItemRepository $itemRepository
        )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param CreateLoanCommand $command
     */
    public function __invoke(CreateLoanCommand $command)
    {
        try {
            $item = $this->itemRepository->getItem($command->getItemId());
            $loan = new Loan(
                $command->getId(),
                $item,
                $command->getLoaner(),
                $command->getLoanDate(),
                $command->getReturnDate()
                );
            $this->repository->save($loan);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LoanNotCreatedException('Loan was not created: '.$e->getMessage());
        }

    }

}
