<?php

//src\Mana\ClientBundle\Entity\CenterRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CenterRepository
 *
 * @author George
 */
class CenterRepository extends EntityRepository
{
    public function colLabels($dateCriteria)
    {
        $str = "select distinct r.center from center r
            join contact c on c.center_id = r.id
            where c.contact_date BETWEEN __DATE_CRITERIA__ 
            order by center";
        $sql = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);
        $colArray = $stmt->fetchAll();
        $colLabels = [];
        foreach ($colArray as $array) {
            $colLabels[] = $array['center'];
        }
        
        return $colLabels;
    }
}
