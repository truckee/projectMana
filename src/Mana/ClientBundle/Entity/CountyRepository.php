<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\CountyRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CountyRepository
 *
 */
class CountyRepository extends EntityRepository
{
    public function colLabels($dateCriteria)
    {
        $str = "select distinct cty.county from county cty
            join contact c on c.county_id = cty.id
            where c.contact_date BETWEEN __DATE_CRITERIA__ 
            order by county";
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $colArray = $stmt->fetchAll();
        $colLabels = [];
        foreach ($colArray as $array) {
            $colLabels[] = $array['county'];
        }
        
        return $colLabels;
    }
    
}
