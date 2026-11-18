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
        $item = parent::findById($id);
        return $item instanceof Member ? $item : null;
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
        if (method_exists(Member::class, 'fromArray')) {
            return Member::fromArray($data);
        }

        return new Member(
            $data['id'],
            $data['name'] ?? $data['nama'] ?? '',
            $data['email'],
            $data['password'] ?? $data['passwordHash'] ?? '',
            (int) ($data['maxRentLimit'] ?? 3)
        );
    }
}

