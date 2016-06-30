<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mana\ClientBundle\Utilities;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\UserManipulator as Manipulator;

/**
 * Executes some manipulations on the users.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class UserManipulator extends Manipulator
{
    /**
     * User manager.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param bool   $active
     * @param bool   $superadmin
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function create($username, $password, $email, $active, $superadmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setFname($this->firstname);
        $user->setSname($this->lastname);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled((Boolean) $active);
        $this->userManager->updateUser($user, true);

        return $user;
    }

    public function setFname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setSname($lastname)
    {
        $this->lastname = $lastname;
    }
}
