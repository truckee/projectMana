<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Utilities\Tools.php

namespace Mana\ClientBundle\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Description of Tools
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class Tools
{
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function makeHouseholdVersion($param)
    {
        
    }
}
