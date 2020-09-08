<?php


namespace Frontend\User\Authentication;


use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Exception;

class PersistentAuthenticationAdapterFactory
{
    /**
     * @param ContainerInterface $container
     * @return PersistentAuthenticationAdapter
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): PersistentAuthenticationAdapter
    {
        if (! $container->has(EntityManager::class)) {
            throw new Exception('EntityManager not found.');
        }

        /** @var array $config */
        $config = $container->get('config');
        if (! isset($config['doctrine']['authentication'])) {
            throw new Exception('Authentication config not found.');
        }
        return new PersistentAuthenticationAdapter(
            $container->get(EntityManager::class),
            $config['doctrine']['authentication']
        );
    }
}