<?php
namespace App\DTO;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class LoanDTO {
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Uuid()
     */
    private $id;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $itemId;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $loaner;
    /**
     * @var \DateTime|null
     * @Type("\DateTime")
     */
    private $loanDate;
    /**
     * @var \DateTime|null
     * @Type("\DateTime")
     */
    private $returnDate;

    /**
     * LoanDTO constructor.
     * @param string|null $id
     * @param string $itemId
     * @param string|null $loaner
     * @param \DateTime|null $loanDate
     * @param \DateTime|null $returnDate

     */
    public function __construct(?string $id,
                                string $itemId,
                                ?string $loaner,
                                ?\DateTime $loanDate,
                                ?\DateTime $returnDate)
    {
        $this->id = $id;
        $this->itemId = $itemId;
        $this->loaner = $loaner;
        $this->loanDate = $loanDate;
        $this->returnDate = $returnDate;
    }

    /**
     * @return ?UuidInterface
     */
    public function getId(): ?UuidInterface
    {
        if (!empty($this->id)) {
            return Uuid::fromString($this->id);
        }

        return null;
    }

    /**
     * @param string|null $id
    */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UuidInterface
     */
    public function getItemId(): UuidInterface
    {
        return Uuid::fromString($this->itemId);
    }

    /**
     * @return string|null
     */
    public function getLoaner(): ?string
    {
        return $this->loaner;
    }

    /**
     * @return \DateTime|null
     */
    public function getLoanDate(): ?\DateTime
    {
        return $this->loanDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getReturnDate(): ?\DateTime
    {
        return $this->returnDate;
    }
}