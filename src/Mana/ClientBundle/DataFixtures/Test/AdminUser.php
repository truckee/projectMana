<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\DataFixtures\Test\AdminUser.php

namespace Mana\ClientBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mana\ClientBundle\Entity\User;
use Mana\ClientBundle\Entity\Ethnicity;
use Mana\ClientBundle\Entity\Center;
use Mana\ClientBundle\Entity\County;
use Mana\ClientBundle\Entity\FsStatus;

/**
 * Description of AdminUser
 *
 * @author George
 */
class AdminUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($userAdmin);
        $password = $encoder->encodePassword('pmana314', $userAdmin->getSalt());
        $userAdmin->setPassword($password);
        $userAdmin->setRole('ROLE_ADMIN');
        $userAdmin->setFname('Benny');
        $userAdmin->setSname('Borko');
        $userAdmin->setEmail('bborko@bogus.info');
        $userAdmin->setIsActive(TRUE);
        $manager->persist($userAdmin);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}
