<?php

namespace App\Auth\Domain\Entity;

use App\Auth\Domain\Enum\SecurityTokenType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'security_tokens')]
#[ORM\Index(columns: ['token'], name: 'idx_token_lookup')]
class SecurityToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'securityTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(length: 100, unique: true)]
    private string $token;

    #[ORM\Column(length: 20, type: 'string', enumType: SecurityTokenType::class)]
    private SecurityTokenType $type;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    public function __construct(User $user, string $token, SecurityTokenType $type, int $ttlInHours = 24)
    {
        $this->user = $user;
        $this->token = $token;
        $this->type = $type;
        $this->expiresAt = new \DateTimeImmutable("+{$ttlInHours} hours");
    }

    public function isValid(): bool
    {
        return $this->expiresAt > new \DateTimeImmutable();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): SecurityTokenType
    {
        return $this->type;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
