<?php

namespace App\Entity\Enum;

use Spatie\Enum\Enum;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;

/**
 * @method static self active()
 * @method static self suspended()
 * @ORM\Embeddable
 */
class UserStatusEnum extends Enum
{
     /**
      * @ORM\Column(type = "string")
      * @Type("string")
      * @var string
      */
    protected $value;
}
