<?php

namespace Frontend\User\Entity;

use Frontend\App\Common\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Laminas\Validator\Date;

/**
 * Class UserToken
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserTokenRepository")
 * @ORM\Table(name="user_token")
 * @package Frontend\User\Entity
 */
class UserToken extends AbstractEntity
{
    /**
     * @ORM\Column(name="value", type="text", nullable=false)
     * @var string $value
     */
    protected string $value;

    /**
     * @ORM\Column(name="expireAt", type="datetime", length=191, nullable=false)
     * @var DateTime $expireAt
     */
    protected DateTime $expireAt;

    /**
     * Many tokens belong to one user. This is the owning side.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tokens")
     * @ORM\JoinColumn(name="userUuid", referencedColumnName="uuid", nullable=false)
     */
    protected User $user;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpireAtFormatted(): string
    {
        return $this->expireAt->format('Y-m-d H:i:s');
    }

    public function getExpireAt(): DateTime
    {
        return $this->expireAt;
    }

    /**
     * @param string $expireAt
     * @return $this
     */
    public function setExpireAt(string $expireAt): self
    {
        $this->expireAt = DateTime::createFromFormat('U', $expireAt);
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        return ! $this->expireAt->getTimestamp() > (new DateTime())->getTimestamp();
    }

    public function getArrayCopy()
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'expireAt' => $this->getExpireAt(),
            'value' => $this->getValue(),
            'user' => $this->getUser()->getName(),
            'isExpired' => $this->isTokenExpired(),
        ];
    }
}
