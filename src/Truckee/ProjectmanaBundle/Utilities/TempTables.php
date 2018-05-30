<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\TempTables.php

namespace Truckee\ProjectmanaBundle\Utilities;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of TempTables
 *
 * @author George
 */
class TempTables
{
    private $conn;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->conn = $em->getConnection();
    }

    /**
     * Fill temp tables with date-specific data
     *
     * @param array $criteria
     */
    public function makeTempTables($criteria)
    {
        /*
         * establish tables for basis of calculations
         */
        $db = $this->conn;
        $whereClause = $criteria['tableWhereClause'];
        // dates are now date strings
        $parameters = $criteria['parameters'];

        //truncate tables
        $dbPlatform = $this->conn->getDatabasePlatform();
        $contactMetaData = $this->em->getClassMetadata('TruckeeProjectmanaBundle:TempContact');
        $memberMetaData = $this->em->getClassMetadata('TruckeeProjectmanaBundle:TempMember');
        $householdMetaData = $this->em->getClassMetadata('TruckeeProjectmanaBundle:TempHousehold');

        $qContact = $dbPlatform->getTruncateTableSql($contactMetaData->getTableName());
        $qMember = $dbPlatform->getTruncateTableSql($memberMetaData->getTableName());
        $qHousehold = $dbPlatform->getTruncateTableSql($householdMetaData->getTableName());
        $this->conn->executeUpdate($qContact);
        $this->conn->executeUpdate($qMember);
        $this->conn->executeUpdate($qHousehold);

        $db->exec('ALTER TABLE temp_contact AUTO_INCREMENT = 0');
        $db->exec('ALTER TABLE temp_member AUTO_INCREMENT = 0');
        $db->exec('ALTER TABLE temp_household AUTO_INCREMENT = 0');

        $sqlContact = 'insert into temp_contact
            (contactdesc_id, household_id, contact_date, first, center_id, county_id)
            select contactdesc_id, household_id, contact_date, first, center_id, cty.id
            from contact c
            join center r on  r.id = c.center_id
            join county cty on cty.id = r.county_id '
            . $whereClause;
        $stmtContact = $db->prepare($sqlContact);
        $stmtContact->execute($parameters);

        //note use of custom MySQL age() function
        $sqlMember = "INSERT INTO temp_member
            (id, household_id, sex, age, ethnicity_id)
            select distinct m.id, m.household_id, sex,
            age(m.dob, :start), ethnicity_id
            from member m
            join temp_contact ct on m.household_id = ct.household_id where
            (exclude_date > :start or exclude_date is null) and (dob < :start or dob is null)";
        $stmtMember = $db->prepare($sqlMember);
        $start = ['start' => $parameters['startDate'], 'start' => $parameters['startDate'], 'start' => $parameters['startDate']];
        $stmtMember->execute($start);

        //note use of custom MySQL res(), size() functions
        $sqlHousehold = "INSERT INTO temp_household
            (id, hoh_id, res, size, size_text, date_added)
            select distinct h.id, hoh_id,
            res(h.id, :start),
            size(h.id, :start), size_text(h.id, :start), date_added from household h
            join temp_contact c on c.household_id = h.id";
        $stmtHousehold = $db->prepare($sqlHousehold);
        $start = ['start' => $parameters['startDate'], 'start' => $parameters['startDate']];
        $stmtHousehold->execute($start);
    }
}
