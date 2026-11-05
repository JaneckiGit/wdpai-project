<?php

declare(strict_types=1);

namespace App\Form;

/* Filtry listy użytkowników w panelu administratora (GET): fraza (imię lub e-mail) i grupa. */
final class UserFilterForm extends Form
{
    public const GROUPS = ['all' => 'Wszyscy', 'hosts' => 'Gospodarze', 'blocked' => 'Zablokowani'];

    public readonly string $query;
    public readonly string $group;

    public function __construct(array $input = [])
    {
        $this->query = self::text($input, 'q');
        $this->group = self::textOr($input, 'group', 'all');
    }

    protected function validate(): void
    {
        $this->requireMaxLength('q', $this->query, 100, 'Wyszukiwana fraza może mieć maksymalnie 100 znaków.');
        $this->requireChoice('group', $this->group, array_keys(self::GROUPS), 'Wybierz grupę użytkowników z listy.');
    }
}
