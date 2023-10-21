<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Tricks;

class AuthenticationService{

    public function isTrickOwnedByUser(Tricks $trick, User $user): bool
    {
        return $trick && $trick->getCreatedBy() === $user;
    }
}