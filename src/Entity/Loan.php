<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Swagger\Annotations as SWG;
use App\Traits\HasTimestamps;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoanRepository")
 * @ORM\HasLifecycleCallbacks()
 * @SWG\Definition(
 *     type="object",
 *     required={"item", "loaner"}
 * )
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Loan implements \JsonSerializable
{
    use HasTimestamps;

    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     * @SWG\Property(description="UUID", type="string", readOnly=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="loaned", cascade={"persist"})
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $loaner;

    /**
     * @ORM\Column(name="loan_date", type="datetime", nullable=true)
     */
    private $loanDate;

    /**
     * @ORM\Column(name="return_date", type="datetime", nullable=true)
     */
    private $returnDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @param UuidInterface $id
     * @param Item $item
     * @param string $loaner
     * @param \DateTime $loanDate
     * @param \DateTime $returnDate
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        UuidInterface $id,
        Item $item,
        ?string $loaner,
        ?\DateTime $loanDate,
        ?\DateTime $returnDate
        )
    {
        $this->setId($id);
        $this->setItem($item);
        $this->setLoaner($loaner);
        $this->setLoanDate($loanDate);
        $this->setReturnDate($returnDate);
    }

    /**
     * @param UuidInterface $id
     * @return Loan
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return Loan
     */
    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getLoaner(): ?string
    {
        return $this->loaner;
    }

    /**
     * @param ?string $loaner
     * @return Loan
     */
    public function setLoaner(?string $loaner): self
    {
        $this->loaner = $loaner;

        return $this;
    }

    public function getLoanDate(): ?\DateTime
    {
        return $this->loanDate;
    }

    /**
     * @param ?\DateTime $loanDate
     * @return Loan
     */
    public function setLoanDate(?\DateTime $loanDate): self
    {
        $this->loanDate = $loanDate;

        return $this;
    }

    public function getReturnDate(): ?\DateTime
    {
        return $this->returnDate;
    }

    /**
     * @param ?\DateTime $returnDate
     * @return Loan
     */
    public function setReturnDate(?\DateTime $returnDate): self
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
       return $this->deletedAt;
    }

    /**
     * @param ?\DateTime $deletedAt
     * @return Loan
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
       $this->deletedAt = $deletedAt;

       return $this;
    }

    /**
     * @param ?\DateTime $createdAt
     * @return Loan
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
       $this->createdAt = $createdAt;

       return $this;
    }

    /**
     * @param ?\DateTime $updatedAt
     * @return Loan
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
       $this->updatedAt = $updatedAt;

       return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
       return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
       return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->loaner;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        return [
            'id' => $this->getId()->toString(),
            'itemId' => $this->getItem()->getId()->toString(),
            'loaner' => $this->getLoaner(),
            'loanDate' => $this->getLoanDate(),
            'returnDate' => $this->getReturnDate(),
        ];
    }
}
