<?php

declare(strict_types=1);

namespace App\Model;

/* Encja konta (Laboratorium 8). Każde konto jest najemcą; gospodarzem i administratorem zostaje się osobno. */
final class User
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private bool $isHost,
        private bool $isAdmin,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /* Imię do powitania i formularzy, np. „Piotr” z „Piotr Nowak”. */
    public function getFirstName(): string
    {
        return explode(' ', $this->name)[0];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isHost(): bool
    {
        return $this->isHost;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
