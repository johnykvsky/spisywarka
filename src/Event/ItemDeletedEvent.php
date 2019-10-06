<?php

namespace App\Event;

final class ItemDeletedEvent
{
    /** @var string */
    private $id;

    /**
     * ItemDeletedEvent constructor.
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