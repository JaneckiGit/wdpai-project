<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/* Encja konta (Laboratorium 8). Każde konto jest najemcą; gospodarzem i administratorem zostaje się osobno. */
final class User
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private bool $isHost,
        private bool $isAdmin,
        private ?DateTimeImmutable $blockedAt,
        private ?DateTimeImmutable $lastSeenAt,
        // Wyliczane w zapytaniu (EXISTS na host_applications) – panel administratora pokazuje status „Wniosek w toku”.
        private bool $hasPendingApplication,
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

    public function revokeHostRole(): void
    {
        $this->isHost = false;
    }

    public function getBlockedAt(): ?DateTimeImmutable
    {
        return $this->blockedAt;
    }

    public function isBlocked(): bool
    {
        return $this->blockedAt !== null;
    }

    public function block(): void
    {
        $this->blockedAt = new DateTimeImmutable();
    }

    public function unblock(): void
    {
        $this->blockedAt = null;
    }

    public function getLastSeenAt(): ?DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    public function hasPendingApplication(): bool
    {
        return $this->hasPendingApplication;
    }
}
