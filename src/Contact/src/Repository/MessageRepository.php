<?php

declare(strict_types=1);

namespace Frontend\Contact\Repository;

use Frontend\App\Repository\AbstractRepository;
use Frontend\Contact\Entity\Message;
use Doctrine\ORM;

/**
 * Class MessageRepository
 * @package Frontend\Contact\Repository
 */
class MessageRepository extends AbstractRepository
{
    /**
     * @param Message $message
     * @throws ORM\ORMException
     * @throws ORM\OptimisticLockException
     */
    public function saveMessage(Message $message)
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
