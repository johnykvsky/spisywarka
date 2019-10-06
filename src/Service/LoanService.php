<?php
namespace App\Service;

use App\Entity\Loan;
use App\DTO\LoanDTO;
use App\Command\CommandInterface;
use App\Command\CreateLoanCommand;
use App\Command\UpdateLoanCommand;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanService
{
    /**
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

    public function getLoanDTOForItem(UuidInterface $itemId)
    {
        return new LoanDTO(null, $itemId->toString(), null, null, null);
    }

    /**
     * @return CreateLoanCommand|UpdateLoanCommand
     */
    public function getCommand(LoanDTO $loanDTO):  CommandInterface
    {
        if (empty($loanDTO->getId())) {
            return new CreateLoanCommand(
                Uuid::uuid4(),
                $loanDTO->getItemId(),
                $loanDTO->getLoaner(),
                $loanDTO->getLoanDate(),
                $loanDTO->getReturnDate()
            );
        }
        
        return new UpdateLoanCommand(
            $loanDTO->getId(),
            $loanDTO->getItemId(),
            $loanDTO->getLoaner(),
            $loanDTO->getLoanDate(),
            $loanDTO->getReturnDate()
        );
    }
}