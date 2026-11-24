<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/* Konto użytkownika z tabeli users. Każde konto jest najemcą; gospodarzem i administratorem bywa dodatkowo. */
final class User
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $email,
        private bool $isHost,
        private bool $isAdmin,
        private ?DateTimeImmutable $blockedAt,
        private ?DateTimeImmutable $lastSeenAt,
        private bool $hasPendingApplication,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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

    public function isBlocked(): bool
    {
        return $this->blockedAt !== null;
    }

    public function getBlockedAt(): ?DateTimeImmutable
    {
        return $this->blockedAt;
    }

    /* NULL, gdy konto jeszcze nigdy nie było używane. */
    public function getLastSeenAt(): ?DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    /* Czy konto ma wniosek o rolę gospodarza, który czeka na decyzję administratora. */
    public function hasPendingApplication(): bool
    {
        return $this->hasPendingApplication;
    }

    public function block(): void
    {
        $this->blockedAt = new DateTimeImmutable();
    }

    public function unblock(): void
    {
        $this->blockedAt = null;
    }

    public function revokeHostRole(): void
    {
        $this->isHost = false;
    }
}
