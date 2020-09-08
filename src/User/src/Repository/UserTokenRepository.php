<?php

namespace Frontend\User\Repository;

use Doctrine\ORM\EntityRepository;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserToken;
use Exception;

class UserTokenRepository extends EntityRepository
{
    /**
     * @param array $data
     * @return UserToken|string
     */
    public function save(array $data)
    {
        try {
            $token = new UserToken();
            $token->setExpireAt($data['expireAt']);
            $token->setValue($data['value']);
            $token->setUser($data['user']);
            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();
            return $token;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param UserToken $token
     * @return string|null
     */
    public function delete(UserToken $token)
    {
        try {
            $this->getEntityManager()->remove($token);
            $this->getEntityManager()->flush();
            return null;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
