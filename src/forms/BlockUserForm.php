<?php

declare(strict_types=1);

namespace App\Form;

/* Powód blokady pokazuje się w tabeli użytkowników, więc administrator musi go podać. */
final class BlockUserForm extends Form
{
    public const MAX_REASON_LENGTH = 200;

    public readonly string $reason;

    public function __construct(array $input = [])
    {
        $this->reason = self::text($input, 'reason');
    }

    protected function validate(): void
    {
        if ($this->reason === '') {
            $this->addError('reason', 'Podaj powód blokady.');
        }

        $this->requireMaxLength('reason', $this->reason, self::MAX_REASON_LENGTH, 'Powód blokady może mieć najwyżej ' . self::MAX_REASON_LENGTH . ' znaków.');
    }
}
