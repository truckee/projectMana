<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserListener
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        EntityManager $entityManager,
        UserPasswordEncoder $passwordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();
        $user = $event->getAuthenticationToken()->getUser();

        if ($user->getEncoderName() === 'old') {
            $user->setEncoderName('new');
            $password = $request->request->get('_password') ;
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $password)
            );

            $this->entityManager->flush();
        }
    }
}