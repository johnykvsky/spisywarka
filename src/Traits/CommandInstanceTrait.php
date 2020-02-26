<?php
namespace App\Traits;

use Ramsey\Uuid\UuidInterface;
use ReflectionClass;

trait CommandInstanceTrait
{
    /**
     * @param UuidInterface|null $uuid
     * @param string $name
     * @return ReflectionClass
     */
    private function getCommandInstance(?UuidInterface $uuid, string $name): ReflectionClass
    {
        if (empty($uuid)) {
            $class = new ReflectionClass('\App\Command\Create'.$name.'Command');
        } else {
            $class = new ReflectionClass('\App\Command\Update'.$name.'Command');
        }

        return $class;
    }
}
