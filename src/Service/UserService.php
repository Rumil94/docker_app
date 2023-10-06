<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function list(): array
    {
        $users = $this->userRepository->findBy([], ['id' => 'desc']);
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ];
        }
        return $data ?? [];
    }

    public function get(int $id): User
    {
        return $this->userRepository->find($id);
    }
}