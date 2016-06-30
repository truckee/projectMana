<?php

/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
//src\Mana\ClientBundle\Entity\FsStatusRepository.php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FsStatusRepository.
 */
class FsStatusRepository extends EntityRepository
{
    public function crossTabData($dateCriteria, $profileType)
    {
        $str = "SELECT r.__TYPE__ colLabel, if(fs.status='Yes', 'Yes', 'No') rowLabel, COUNT(DISTINCT h.id) N ".
                    'FROM household h '.
                    'JOIN contact c ON c.household_id = h.id '.
                    'LEFT JOIN __TYPE__ r ON r.id = c.__TYPE___id '.
                    'JOIN fs_status fs ON h.foodstamp_id = fs.id '.
                    'WHERE c.contact_date BETWEEN __DATE_CRITERIA__ '.
                    'GROUP BY colLabel, rowLabel';
        $sql1 = str_replace('__DATE_CRITERIA__', $dateCriteria, $str);
        $sql = str_replace('__TYPE__', $profileType, $sql1);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql);

        return $stmt->fetchAll();
    }
}
