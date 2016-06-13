<?php

/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Mana\ClientBundle\Tests\Controller\ManaWebTestCase.php

namespace Mana\ClientBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Description of ManaWebTestCase
 *
 * @author George
 */
class ManaWebTestCase extends WebTestCase
{

    protected function loadFixtures(array $classNames, $omName = null, $registryName = 'doctrine', $purgeMode = null)
    {
        $this->getContainer()->get('doctrine')->getManager()->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $result = parent::loadFixtures($classNames, $omName, $registryName, $purgeMode);
        $this->getContainer()->get('doctrine')->getManager()->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));
        return $result;
    }

}
