<?php

namespace App\Auth\Infrastructure\Persistence\Doctrine;

use App\Auth\Domain\Entity\SecurityToken;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Enum\SecurityTokenType;
use App\Auth\Domain\Repository\SecurityTokenRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineSecurityTokenRepository extends ServiceEntityRepository implements SecurityTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecurityToken::class);
    }

    public function findByValue(string $token): ?SecurityToken
    {
        return $this->findOneBy(['token' => $token]);
    }

    public function findByTokenAndUser(string $token, User $user, SecurityTokenType $type): ?SecurityToken
    {
        return $this->findOneBy([
            'token' => $token,
            'user' => $user,
            'type' => $type
        ]);
    }

    public function save(SecurityToken $token): void
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

    public function delete(SecurityToken $token): void
    {
        $this->getEntityManager()->remove($token);
        $this->getEntityManager()->flush();
    }

    public function deleteExpiredTokens(): int
    {
        $query = $this->getEntityManager()->createQuery(
            'DELETE FROM App\Auth\Domain\Entity\SecurityToken t WHERE t.expiresAt < :now'
        )->setParameter('now', new \DateTimeImmutable());

        return $query->execute();
    }
}
