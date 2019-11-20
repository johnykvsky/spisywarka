<?php
namespace App\Service;

use App\Entity\User;
use App\DTO\UserDTO;
use App\Command\CommandInterface;
use App\Command\CreateUserCommand;
use App\Command\UpdateUserCommand;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @param User $user
     * @return UserDTO
     */
    public function fillUserDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getId()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getStatus(),
            $user->getPlainPassword()
        );
    }

    /**
     * @param UserDTO $userDTO
     * @return CreateUserCommand|UpdateUserCommand
     */
    public function getCommand(UserDTO $userDTO):  CommandInterface
    {
        if (empty($userDTO->getId())) {
            return new CreateUserCommand(
                Uuid::uuid4(),
                $userDTO->getFirstName(),
                $userDTO->getLastName(),
                $userDTO->getEmail(),
                $userDTO->getStatus(),
                $userDTO->getPlainPassword()
            );
        } else {
            return new UpdateUserCommand(
                $userDTO->getId(),
                $userDTO->getFirstName(),
                $userDTO->getLastName(),
                $userDTO->getEmail(),
                $userDTO->getStatus(),
                $userDTO->getPlainPassword()
            );
        }
    }
}