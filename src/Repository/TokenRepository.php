<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\TokenType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class TokenRepository extends EntityRepository
{
    public function findValidToken(?string $value = null, ?User $user = null): ?Token
    {
        $queryBuilder = $this->createQueryBuilder('token');

        $andX = [
            $queryBuilder->expr()->gt('token.expireAt', ':now'),
            $queryBuilder->expr()->eq('token.type', ':type')
        ];
        $parameters = ['now' => new \DateTime(), 'type' => TokenType::ForgotPassword];

        if (null !== $value) {
            $andX[] = $queryBuilder->expr()->eq('token.value', ':value');
            $parameters['value'] = $value;
        }

        if (null !== $user) {
            $andX[] = $queryBuilder->expr()->eq('token.user', ':user');
            $parameters['user'] = $user;
        }

        $queryBuilder->where($queryBuilder->expr()->andX(...$andX));
        foreach ($parameters as $name => $value) {
            $queryBuilder->setParameter($name, $value);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function deleteTokens(TokenType $type, User $user): void
    {
        $queryBuilder = $this->createQueryBuilder('token');

        $queryBuilder->delete()
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('token.type', ':type'),
                    $queryBuilder->expr()->eq('token.user', ':user')
                )
            )
            ->setParameter('type', $type)
            ->setParameter('user', $user);

        $queryBuilder->getQuery()->execute();
    }
}