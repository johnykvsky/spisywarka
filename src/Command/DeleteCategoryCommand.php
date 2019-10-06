<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class DeleteCategoryCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
