<?php

namespace App\Event;

final class ItemCreatedEvent
{
    /** @var string */
    private $id;

    /**
     * ItemCreatedEvent constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
