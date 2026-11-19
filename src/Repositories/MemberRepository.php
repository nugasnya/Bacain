<?php

declare(strict_types=1);

namespace Bacain\Repositories;

use Bacain\Models\Member;

class MemberRepository extends JsonRepository
{
    public function __construct(string $filePath = __DIR__ . '/../../data/members.json')
    {
        parent::__construct($filePath);
    }

    /**
     * @return Member[]
     */
    public function findAll(): array
    {
        $records = $this->readData();
        return array_map(fn(array $row): Member => $this->mapToEntity($row), $records);
    }

    public function findById(string $id): ?Member
    {
        foreach ($this->findAll() as $member) {
            if ((string) $member->getId() === $id) {
                return $member;
            }
        }
        return null;
    }

    public function findByEmail(string $email): ?Member
    {
        $normalizedEmail = strtolower(trim($email));

        foreach ($this->findAll() as $member) {
            if (strtolower($member->getEmail()) === $normalizedEmail) {
                return $member;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapToEntity(array $data): Member
    {
        return Member::fromArray($data);
    }
}

