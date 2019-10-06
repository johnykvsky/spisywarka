<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use Ramsey\Uuid\Uuid;

class CreateLoanRequest
{
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $itemId;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $loaner;
    /**
     * @var string|null 
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $loanDate;
    /**
     * @var string|null 
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $returnDate;

    /**
     * CreateLoanRequest constructor.
     * @param string $itemId
     * @param string $loaner
     * @param string|null $loanDate
     * @param string|null $returnDate
     */
    public function __construct(
        string $itemId, 
        string $loaner,
        ?string $loanDate,
        ?string $returnDate
        )
    {
        $this->itemId = $itemId;
        $this->loaner = $loaner;
        $this->loanDate = $loanDate;
        $this->returnDate = $returnDate;
    }

    /**
     * @return UuidInterface
     */
    public function getItemId(): UuidInterface
    {
        return Uuid::fromString($this->itemId);
    }
    
    /**
     * @return string
     */
    public function getLoaner(): string
    {
        return $this->loaner;
    }

    /**
     * @return ?\DateTime
     */
    public function getLoanDate(): ?\DateTime
    {
        if (!empty($this->loanDate)) {
            $loanDate =  \DateTime::createFromFormat('Y-m-d H:i:s', $this->loanDate);
            if ($loanDate !== false) {
                return $loanDate;
            }
        }

        return null;
    }
    
    /**
     * @return ?\DateTime
     */
    public function getReturnDate(): ?\DateTime
    {
        if (!empty($this->returnDate)) {
            $returnDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->returnDate);
            if ($returnDate !== false) {
                return $returnDate;
            }
        }
        
        return null;
    }
}
