<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Tests\TruckeeWebTestCase.php

namespace App\Test;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Description of TruckeeWebTestCase.
 *
 * @author George Brooks
 */
class AppWebTestCase extends WebTestCase
{
    protected function loadFixtures(array $classNames = [], bool $append = false, ?string $omName = null, string $registryName = 'doctrine', ?int $purgeMode = null): ?AbstractExecutor
    {
        $this->getContainer()->get('doctrine')->getManager()->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=0'));
        $result = parent::loadFixtures($classNames, $append, $omName, $registryName, $purgeMode);
        $this->getContainer()->get('doctrine')->getManager()->getConnection()->query(sprintf('SET FOREIGN_KEY_CHECKS=1'));

        return $result;
    }
}
