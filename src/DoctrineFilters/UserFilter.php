<?php

namespace App\DoctrineFilters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class UserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the LocalAware interface
        if (!in_array('App\Entity\UserAwareInterface', $targetEntity->reflClass->getInterfaceNames())) {
            return "";
        }

        return $targetTableAlias.'.user_id = ' . $this->getParameter('userId'); // getParameter applies quoting automatically
    }
}