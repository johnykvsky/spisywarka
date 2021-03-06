<?php
namespace App\Service;

use App\Entity\Loan;
use App\DTO\LoanDTO;
use App\Command\CommandInterface;
use App\Command\CreateLoanCommand;
use App\Command\UpdateLoanCommand;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Traits\CommandInstanceTrait;

class LoanService
{
    use CommandInstanceTrait;

    /**
     * @param Loan $loan
     * @return LoanDTO
     */
    public function fillLoanDTO(Loan $loan): LoanDTO
    {
        return new LoanDTO(
            $loan->getId()->toString(),
            $loan->getItem()->getId()->toString(),
            $loan->getLoaner(),
            $loan->getLoanDate(),
            $loan->getReturnDate()
        );
    }

    /**
     * @param UuidInterface $itemId
     * @return LoanDTO
     */
    public function getLoanDTOForItem(UuidInterface $itemId): LoanDTO
    {
        return new LoanDTO(null, $itemId->toString(), null, null, null);
    }

    /**
     * @param LoanDTO $loanDTO
     * @return CreateLoanCommand|UpdateLoanCommand
     */
    public function getCommand(LoanDTO $loanDTO):  CommandInterface
    {
        $command = $this->getCommandInstance($loanDTO->getId(), 'Loan');
        return $command->newInstanceArgs([
            $loanDTO->getId() ?? Uuid::uuid4(),
            $loanDTO->getItemId(),
            $loanDTO->getLoaner(),
            $loanDTO->getLoanDate(),
            $loanDTO->getReturnDate()
        ]);
    }
}