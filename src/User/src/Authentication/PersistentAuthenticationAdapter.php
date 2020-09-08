<?php

namespace Frontend\User\Authentication;

use Doctrine\ORM\EntityManager;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserIdentity;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Exception;

class PersistentAuthenticationAdapter implements AdapterInterface
{
    private const METHOD_NOT_EXISTS = "Method %s not found in %s .";
    private const OPTION_VALUE_NOT_PROVIDED = "Option '%s' not provided for '%s' option.";

    /** @var string $identity */
    private $identity;

    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var array $config */
    private $config;

    /**
     * AuthenticationAdapter constructor.
     * @param $entityManager
     * @param array $config
     */
    public function __construct($entityManager, array $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    /**
     * @param string $identity
     * @return $this
     */
    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return string
     */
    private function getIdentity(): string
    {
        return $this->identity;
    }

    /**
     * @return Result
     * @throws Exception
     */
    public function authenticate(): Result
    {
        /** Check for the authentication configuration */
        $this->validateConfig();

        /** Get the identity class object */
        $repository = $this->entityManager->getRepository($this->config['orm_default']['identity_class']);

        /** @var User $identityClass */
        $identityClass = $repository->findOneBy([
            $this->config['orm_default']['identity_property'] => $this->getIdentity()
        ]);

        if (null === $identityClass) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                [$this->config['orm_default']['messages']['not_found']]
            );
        }

        /** Check for extra validation options */
        if (! empty($this->config['orm_default']['options'])) {
            foreach ($this->config['orm_default']['options'] as $property => $option) {
                /** @var callable $methodName */
                $methodName = "get" . ucfirst($property);

                /** Check if the method exists in the provided identity class */
                $this->checkMethod($identityClass, $methodName);

                /** Check if value for the current option is provided */
                if (! array_key_exists('value', $option)) {
                    throw new Exception(sprintf(
                        self::OPTION_VALUE_NOT_PROVIDED,
                        'value',
                        $property
                    ));
                }

                /** Check if message for the current option is provided */
                if (! array_key_exists('message', $option)) {
                    throw new Exception(sprintf(
                        self::OPTION_VALUE_NOT_PROVIDED,
                        'message',
                        $property
                    ));
                }

                if ($identityClass->$methodName() !== $option['value']) {
                    return new Result(
                        Result::FAILURE,
                        null,
                        [$option['message']]
                    );
                }
            }
        }

        return new Result(
            Result::SUCCESS,
            new UserIdentity(
                $identityClass->getUuid()->toString(),
                $identityClass->getIdentity(),
                $identityClass->getRoles()->toArray(),
                $identityClass->getDetail()->getArrayCopy(),
            ),
            [$this->config['orm_default']['messages']['success']]
        );
    }

    /**
     * @throws Exception
     */
    private function validateConfig()
    {
        if (
            ! isset($this->config['orm_default']['identity_class']) ||
            ! class_exists($this->config['orm_default']['identity_class'])
        ) {
            throw new Exception("No or invalid param 'identity_class' provided.");
        }

        if (! isset($this->config['orm_default']['identity_property'])) {
            throw new Exception("No or invalid param 'identity_class' provided.");
        }
    }

    /**
     * @param $identityClass
     * @param string $methodName
     * @throws Exception
     */
    private function checkMethod($identityClass, string $methodName): void
    {
        if (! method_exists($identityClass, $methodName)) {
            throw new Exception(sprintf(
                self::METHOD_NOT_EXISTS,
                $methodName,
                get_class($identityClass)
            ));
        }
    }
}
