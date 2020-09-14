<?php

declare(strict_types=1);

namespace Frontend\User\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Frontend\App\Repository\AbstractRepository;
use Frontend\User\Entity\UserRole;

/**
 * Class UserRoleRepository
 * @package Frontend\User\Repository
 */
class UserRoleRepository extends AbstractRepository
{
    /**
     * @param string $name
     * @return UserRole|null
     * @throws NonUniqueResultException
     */
    public function findByName(string $name): ?UserRole
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('role')
            ->from(UserRole::class, 'role')
            ->andWhere('role.name = :name')
            ->setParameter('name', $name);

        return $qb->getQuery()->useQueryCache(true)->getOneOrNullResult();
    }
}
