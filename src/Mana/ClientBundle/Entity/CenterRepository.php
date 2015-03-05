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
    public function activeCenters()
    {
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT c FROM ManaClientBundle:Center c "
                . "WHERE c.center <> 'N/A' "
                . "ORDER BY c.center ASC")
                ->getResult();
    }
}
