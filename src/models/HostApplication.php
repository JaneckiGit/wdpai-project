<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/* Wniosek o rolę gospodarza z danymi wnioskodawcy (z konta), firmy (po NIP-ie) i pierwszego lokalu. */
final class HostApplication
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private string $applicantName,
        private string $email,
        private string $phone,
        private string $companyNip,
        private string $companyName,
        private string $venueName,
        private string $venueType,
        private string $street,
        private string $buildingNumber,
        private ?string $unitNumber,
        private string $postalCode,
        private string $city,
        private string $message,
        private string $status,
        // Datę wysłania nadaje baza, więc nowy wniosek (jeszcze bez id) jej nie ma.
        private ?DateTimeImmutable $createdAt = null,
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

    public function getApplicantName(): string
    {
        return $this->applicantName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCompanyNip(): string
    {
        return $this->companyNip;
    }

    /* NIP w zapisie z myślnikami, np. 6762481035 → 676-248-10-35. */
    public function getFormattedNip(): string
    {
        return substr($this->companyNip, 0, 3) . '-' . substr($this->companyNip, 3, 3) . '-'
            . substr($this->companyNip, 6, 2) . '-' . substr($this->companyNip, 8, 2);
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getVenueName(): string
    {
        return $this->venueName;
    }

    public function getVenueType(): string
    {
        return $this->venueType;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getBuildingNumber(): string
    {
        return $this->buildingNumber;
    }

    public function getUnitNumber(): ?string
    {
        return $this->unitNumber;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    /* Adres lokalu z wniosku, np. „ul. Józefińska 12, 30-529 Kraków”. */
    public function getAddress(): string
    {
        return $this->street . ' ' . $this->buildingNumber . ($this->unitNumber === null ? '' : '/' . $this->unitNumber)
            . ', ' . $this->postalCode . ' ' . $this->city;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /* Pełne godziny od wysłania wniosku – panel wyróżnia wnioski czekające co najmniej dobę. */
    public function getHoursWaiting(): int
    {
        return $this->createdAt === null ? 0 : intdiv(time() - $this->createdAt->getTimestamp(), 3600);
    }
}
