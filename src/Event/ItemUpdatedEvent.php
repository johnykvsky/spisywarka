<?php

namespace App\Event;

final class ItemUpdatedEvent
{
    /** @var string */
    private $id;


    /**
     * ItemUpdatedEvent constructor.
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
