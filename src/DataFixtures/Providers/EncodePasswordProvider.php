<?php

namespace App\DataFixtures\Providers;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EncodePasswordProvider
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function encodePassword(string $plainPassword): string
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}
