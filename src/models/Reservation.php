<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final class Reservation
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private int $venueId,
        private string $mode,
        private string $purpose,
        private DateTimeImmutable $date,
        private int $startHour,
        private int $endHour,
        private ?int $seats,
        private string $status,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
        // Nazwy lokalu i najemcy z JOIN-ów – repozytorium je dokłada, nowa rezerwacja ich nie potrzebuje.
        private ?string $venueName = null,
        private ?string $guestName = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getVenueId(): int
    {
        return $this->venueId;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    /* Liczba miejsc w trybie „miejsca do pracy”; przy wyłączności null. */
    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getVenueName(): ?string
    {
        return $this->venueName;
    }

    public function getGuestName(): ?string
    {
        return $this->guestName;
    }
}
