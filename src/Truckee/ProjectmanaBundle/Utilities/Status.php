<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\DBAL\Connection;

/**
 * find active status of households by year and
 * change status on request.
 */
class Status
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create array by year with count of active & inactive households
     *
     * @return array
     */
    public function getYearStatus()
    {
        $sql = 'select max(year(contact_date)) Year, if(active=1,1,0) Active, if(active=0,1,0) Inactive from contact c
            join household h on h.id = c.household_id
            group by household_id';
        $statusYears = $this->conn->fetchAll($sql);
        $statusYearArray = array();
        foreach ($statusYears as $status) {
            if (!array_key_exists($status['Year'], $statusYearArray)) {
                $statusYearArray[$status['Year']]['Active'] = 0;
                $statusYearArray[$status['Year']]['Inactive'] = 0;
            }
            $statusYearArray[$status['Year']]['Active'] += $status['Active'];
            $statusYearArray[$status['Year']]['Inactive'] += $status['Inactive'];
        }
        krsort($statusYearArray);

        return $statusYearArray;
    }

    /**
     * Bulk change of active status
     *
     * @param array $changes
     */
    public function setStatus($changes)
    {
        set_time_limit(0);
        $sql = 'select household_id id  from contact c
            join household h on c.household_id = h.id
            where h.active = ?
            group by c.household_id
            having max(year(contact_date)) = ?';
        foreach ($changes as $year => $action) {
            if ($action == 'active') {
                $stmt = $this->conn->executeQuery($sql, array(0, $year));
                $households = $stmt->fetchAll();
                foreach ($households as $household) {
                    $id = $household['id'];
                    $this->conn->update('household',
                            array('active' => 1),
                            array('id' => $id));
                }
            } else {
                $stmt = $this->conn->executeQuery($sql, array(1, $year));
                $households = $stmt->fetchAll();
                foreach ($households as $household) {
                    $id = $household['id'];
                    $this->conn->update('household',
                            array('active' => 0),
                            array('id' => $id));
                }
            }
        }
    }
}
