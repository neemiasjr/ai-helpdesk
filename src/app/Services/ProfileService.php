<?php

namespace App\Services;

use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Models\User;

class ProfileService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function updateProfile(User $user, array $data): bool
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Se o email mudou, limpar a verificação
        if (isset($data['email']) && $user->email !== $data['email']) {
            $updateData['email_verified_at'] = null;
        }

        return $this->userRepository->update($user, $updateData);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}

