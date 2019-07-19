<?php

/*
 * This file is part of the App package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserListener
{
    private $em;
   private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLogin(new \DateTime());
        $user->setPasswordExpiresAt();
        $this->em->persist($user);
        
        if ($user->getEncoderName() === 'old') {
            $user->setEncoderName('new');
            $password = $request->request->get('_password') ;
            $user->setPassword(
                $this->encoder->encodePassword($user, $password)
            );
        }
            $this->em->flush();
        
    }
}
