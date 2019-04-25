<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{

    // ...

    public function loadUserByUsername($name)
    {
        return $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->orWhere('u.username = :username')
                ->setParameter('username', $name)
                ->setParameter('email', $name)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
