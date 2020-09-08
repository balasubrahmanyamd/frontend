<?php

namespace Frontend\User\Service;

use Doctrine\ORM\EntityManager;
use Dot\Session\Options\SessionOptions;
use Frontend\App\Common\UuidOrderedTimeGenerator;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserIdentity;
use Frontend\User\Entity\UserToken;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserTokenRepository;
use Laminas\Session\SessionManager;
use Dot\AnnotatedServices\Annotation\Inject;
use Exception;

class UserTokenService
{
    public const REMEMBER_ME = "1";

    /** @var EntityManager $em */
    protected $em;

    /** @var UserRepository $userRepository */
    protected $userRepository;

    /** @var UserTokenRepository $userTokenRepository */
    protected $userTokenRepository;

    /** @var SessionManager $sessionManager */
    protected $sessionManager;

    /** @var SessionOptions $options */
    protected $options;

    /** @var array $config */
    protected $config;

    /**
     * UserTokenService constructor.
     * @param EntityManager $em
     * @param SessionManager $sessionManager
     * @param SessionOptions $options
     * @param array $config
     *
     * @Inject({EntityManager::class, SessionManager::class, SessionOptions::class, "config"})
     */
    public function __construct(
        EntityManager $em,
        SessionManager $sessionManager,
        SessionOptions $options,
        array $config = []
    ) {
        $this->em = $em;
        $this->sessionManager = $sessionManager;
        $this->userRepository = $em->getRepository(User::class);
        $this->userTokenRepository = $em->getRepository(UserToken::class);
        $this->options = $options;
        $this->config = $config;
    }

    /**
     * @param UserIdentity $identity
     * @return UserToken|string
     */
    public function createToken(UserIdentity $identity)
    {
        try {
            $cookieValue = UuidOrderedTimeGenerator::generateUuid();
            $sessionConfig = $this->sessionManager->getConfig();
            $now = time();
            $expireAt = $now + $sessionConfig->getCookieLifetime();
            setcookie(
                $this->options->getCookieName(),
                base64_encode(hash('sha256', $cookieValue)),
                $expireAt,
                $sessionConfig->getCookiePath(),
                $sessionConfig->getCookieDomain(),
                (bool) $sessionConfig->getCookieSecure(),
                (bool) $sessionConfig->getCookieHttpOnly()
            );

            /** @var User $user */
            $user = $this->userRepository->findByUuid($identity->getUuid());
            return $this->userTokenRepository->save([
                'expireAt' => $expireAt,
                'value' => hash('sha256', $cookieValue),
                'user' => $user
            ]);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param array $cookies
     * @return bool
     */
    public function destroyToken(array $cookies)
    {
        $key = $this->options->getCookieName();
        $rememberMe = $cookies[$key] ?? null;
        if (is_null($rememberMe)) {
            return false;
        }
        setcookie(
            $key,
            "",
            1,
            $this->sessionManager->getConfig()->getCookiePath()
        );
        $token = $this->userTokenRepository->findOneBy(['value' => $rememberMe]);
        if (! ($token instanceof UserToken)) {
            return false;
        }
        $this->userTokenRepository->delete($token);
        return true;
    }

    /**
     * @param array $data
     * @return object|null
     */
    public function findOneBy(array $data)
    {
        return $this->userTokenRepository->findOneBy($data);
    }
}
