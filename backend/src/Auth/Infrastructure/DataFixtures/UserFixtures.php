<?php

namespace App\Auth\Infrastructure\DataFixtures;

use App\Auth\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'admin@esku.com',
                'password' => 'admin123',
                'roles' => ['ROLE_ADMIN']
            ],
            [
                'email' => 'warehouse@esku.com',
                'password' => 'esku2024',
                'roles' => ['ROLE_MANAGER']
            ],
            [
                'email' => 'driver@esku.com',
                'password' => 'ruta44',
                'roles' => ['ROLE_USER']
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User();

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);

            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
