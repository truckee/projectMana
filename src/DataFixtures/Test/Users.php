<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\DataFixtures\Test\Users.php

namespace App\DataFixtures\Test;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Users.
 *
 * @author George Brooks
 */
class Users extends Fixture implements OrderedFixtureInterface {

    /**
     * @var ContainerInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
//    public function setContainer(ContainerInterface $container = null)
//    {
//        $this->container = $container;
//    }

    public function load(ObjectManager $manager) {
        // Get our userManager, you must implement `ContainerAwareInterface`
//        $userManager = $this->container->get('fos_user.user_manager');

        $admin = new User();
//        $admin->setUsername('admin');
        $admin->setFname('Benny');
        $admin->setSname('Borko');
        $admin->setUsername('admin');
        $admin->setEmail('admin@bogus.info');
        $password = $this->encoder->encodePassword($admin, 'manapw');
        $admin->setPassword($password);
        $admin->setEnabled(true);
        $admin->setRoles(array('ROLE_ADMIN'));

        // Update the user
        $manager->persist($admin);

        $user = new User();
//        $user->setUsername('dberry');
        $user->setFname('Dingle');
        $user->setSname('Berry');
        $user->setUsername('dberry');
        $user->setEmail('dberry@domain.com');
        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_USER'));

        // Update the user
        $manager->persist($user);
        
        // new user invitation
        $invite = new Invitation();
        $invite->setFname('Richard');
        $invite->setSname('Feynmann');
        $invite->setUsername('rfeynmann');
        $invite->setEmail('rfeynmann@bogus.info');
        $invite->setToken('abcdefg');
        
        $manager->persist($invite);
        
        // reset password trial        
        $manager->flush();
    }

    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

}
