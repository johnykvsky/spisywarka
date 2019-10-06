<?php

namespace App\CommandHandler;

use App\Command\DeleteItemCommand;
//use App\Entity\Item;
use App\Repository\ItemRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotDeletedException;
use Psr\Log\LoggerInterface;

class DeleteItemCommandHandler implements CommandHandlerInterface
{
    /**
     * @var ItemRepository
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
     * @param ItemRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, ItemRepository $repository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param DeleteItemCommand $command
     */
    public function __invoke(DeleteItemCommand $command)
    {
        try {
             $item = $this->repository->getItem($command->getId());   

            if ($item->getLoaned()) {
                throw new ItemNotDeletedException('Item has loan record');
            }      
             
            $this->repository->delete($item);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotDeletedException('Item was not deleted: '.$e->getMessage());
        }
    }

}
