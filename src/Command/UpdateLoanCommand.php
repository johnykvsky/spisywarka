<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class UpdateLoanCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var UuidInterface
     */
    private $itemId;
    /**
     * @var ?string
     */
    private $loaner;
    /**
     * @var ?\DateTime
     */
    private $loanDate;
    /**
     * @var ?\DateTime
     */
    private $returnDate;
    
    /**
     * @param UuidInterface $id
     * @param UuidInterface $itemId
     * @param ?string $loaner
     * @param ?\DateTime $loanDate
     * @param \DateTime $returnDate
     */
    public function __construct(
        UuidInterface $id,
        UuidInterface $itemId,
        ?string $loaner,
        ?\DateTime $loanDate,
        ?\DateTime $returnDate
        )
    {
        $this->id = $id;
        $this->itemId = $itemId;
        $this->loaner = $loaner;
        $this->loanDate = $loanDate;
        $this->returnDate = $returnDate;
    }
    
    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
    
    /**
     * @return UuidInterface
     */
    public function getItemId(): UuidInterface
    {
        return $this->itemId;
    }
    
    /**
     * @return string|null
     */
    public function getLoaner(): ?string
    {
        return $this->loaner;
    }
    
    /**
     * @return ?\DateTime
     */
    public function getLoanDate(): ?\DateTime
    {
        return $this->loanDate;
    }
    
    /**
     * @return ?\DateTime
     */
    public function getReturnDate(): ?\DateTime
    {
        return $this->returnDate;
    }
}
