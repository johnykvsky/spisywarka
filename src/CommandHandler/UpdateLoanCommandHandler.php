<?php

namespace App\CommandHandler;

use App\Command\UpdateLoanCommand;
use App\Repository\LoanRepository;
use App\Repository\ItemRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\LoanNotUpdatedException;
use Psr\Log\LoggerInterface;

class UpdateLoanCommandHandler implements CommandHandlerInterface
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
     * @param UpdateLoanCommand $command
     */
    public function __invoke(UpdateLoanCommand $command)
    {
        try {
            $item = $this->itemRepository->getItem($command->getItemId());
            $loan = $this->repository->getLoan($command->getId());
            $loan->setItem($item);
            $loan->setLoaner($command->getLoaner());
            $loan->setLoanDate($command->getLoanDate());
            $loan->setReturnDate($command->getReturnDate());
            $this->repository->save($loan);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LoanNotUpdatedException('Loan was not updated: '.$e->getMessage());
        }

    }

}
