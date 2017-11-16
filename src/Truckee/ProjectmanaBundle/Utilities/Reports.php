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

use Doctrine\ORM\EntityManager;

/**
 * Project MANA statistics.
 */
class Reports
{
    private $conn;
    private $commonCriteria;
    private $ageDist;
    private $ageGenderDist;
    private $details;
    private $em;
    private $ethDist;
    private $familyDist;
    private $freqDist;
    private $newByType;
    private $newMembers;
    private $newHouseholds;
    private $residency;
    private $statistics;
    private $totalHouseholds;
    private $totalIndividuals;
    private $uniqHouseholds;
    private $uniqIndividuals;
    private $uniqNewIndividuals;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->conn = $em->getConnection();
    }

    /**
     * Organize criteria for reports
     *
     * @param array $criteria
     *
     * @return array report & template criteria
     */
    public function setCriteria($criteria)
    {
        $parameters = ['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']];
        $incoming = array(
            'county' => (!empty($criteria['county'])) ? $criteria['county'] : '',
            'center' => (!empty($criteria['center'])) ? $criteria['center'] : '',
        );
        $newWhere = ' WHERE c.contactDate >= :startDate AND c.contactDate <= :endDate';
        $tableWhere = ' WHERE c.contact_date >= :startDate AND c.contact_date <= :endDate';
        $options = array('county', 'center');
        foreach ($options as $opt) {
            if ('' !== $incoming[$opt]) {
                $newWhere .= " and c.$opt = :$opt";
                $tableWhere .= ' and c.' . $opt . '_id = :' . $opt;
                $parameters[$opt] = $incoming[$opt];
            }
        }
        if (!empty($criteria['contact_type'])) {
            $newWhere .= ' and c.contactType = :contactType';
            $tableWhere .= ' and c.contact_type_id  = :contactType';
            $parameters['contactType'] = $criteria['contact_type'];
        }
        //set criteria for common statistics
        $this->commonCriteria['newWhereClause'] = $newWhere;
        $this->commonCriteria['tableWhereClause'] = $tableWhere;
        $this->commonCriteria['parameters'] = $parameters;
    }

    /**
     * Set individual statistics
     *
     * @param array $criteria
     */
    public function setStats($criteria)
    {
        $this->setCriteria($criteria);
        //fill tables for common statistics
        $this->makeTempTables($this->commonCriteria);

        //calculate common statistics
        $this->setNewByType($this->commonCriteria);
        $this->setNewMembers($this->commonCriteria);
        $this->setNewHouseholds();
        $this->setUniqNewIndividuals();
        $this->setUniqHouseholds();
        $this->setResidency();
        $this->setFamilyDist();
        $this->setFreqDist();
        $this->setEthDist();
        $this->setAgeGenderDist();
        $this->setUniqIndividuals();
        $this->setTotalIndividuals();
        $this->setTotalHouseholds();
        $statistics = array();
        foreach ($this->ageDist as $key => $value) {
            $item = $this->ageDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->residency as $key => $value) {
            $statistics[$key] = $value;
        }
        foreach ($this->familyDist as $key => $value) {
            $statistics[$key] = $value;
        }
        foreach ($this->freqDist as $key => $value) {
            $statistics[$key] = $value;
        }
        foreach ($this->ethDist as $key => $value) {
            $statistics[$key] = $value;
        }
        foreach ($this->ageGenderDist as $key => $value) {
            $item = $this->ageGenderDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }

        $uniqNewInd = $this->getUniqNewIndividuals();
        $item = $uniqNewInd['UNI'];
        $statistics['Unique New Individuals'] = (!empty($item)) ? $item : 0;

        $ti = $this->getTotalIndividuals();
        $item = $ti['TIS'];
        $statistics['TIS'] = (!empty($item)) ? $item : 0;

        $uis = $this->getUniqIndividuals();
        $item = $uis['UIS'];
        $statistics['UIS'] = (!empty($item)) ? $item : 0;

        $th = $this->getTotalHouseholds();
        $item = $th['THS'];
        $statistics['THS'] = (!empty($item)) ? $item : 0;

        $uhs = $this->getUniqHouseholds();
        $item = $uhs['UHS'];
        $statistics['UHS'] = (!empty($item)) ? $item : 0;

        $newMembers = $this->getnewMembers();
        $item = $newMembers['NewMembers'];
        $statistics['NewMembers'] = (!empty($item)) ? $item : 0;

        $newHouseholds = $this->getNewHouseholds();
        $item = $newHouseholds['NewHouseholds'];
        $statistics['NewHouseholds'] = (!empty($item)) ? $item : 0;

        $nbt = $this->getNewByType();
        $item = $nbt['NewByType'];
        $statistics['NewByType'] = (!empty($item)) ? $item : 0;

        $this->statistics = $statistics;
    }

    /**
     * Fill temp tables with date-specific data
     *
     * @param array $criteria
     */
    private function makeTempTables($criteria)
    {
        /*
         * establish tables for basis of calculations
         */
        $db = $this->conn;
        $whereClause = $criteria['tableWhereClause'];
        $parameters = $criteria['parameters'];

        $this->em->createQuery('DELETE FROM TruckeeProjectmanaBundle:TempContact')->execute();
        $db->exec('ALTER TABLE temp_contact AUTO_INCREMENT = 0');

        $sqlContact = 'insert into temp_contact
            (contact_type_id, household_id, contact_date, first, center_id, county_id)
            select contact_type_id, household_id, contact_date, first, center_id, cty.id
            from contact c
            join center r on  r.id = c.center_id
            join county cty on cty.id = r.county_id '
            . $whereClause;
        $stmtContact = $db->prepare($sqlContact);
        $stmtContact->execute($parameters);

        $this->em->createQuery('DELETE FROM TruckeeProjectmanaBundle:TempMember')->execute();
        $db->exec('ALTER TABLE temp_member AUTO_INCREMENT = 0');

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

        $this->em->createQuery('DELETE FROM TruckeeProjectmanaBundle:TempHousehold')->execute();
        $db->exec('ALTER TABLE temp_household AUTO_INCREMENT = 0');

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

    protected function getAgeDist()
    {
        return $this->ageDist;
    }

    protected function getAgeGenderDist()
    {
        return $this->ageGenderDist;
    }

    public function getDetails()
    {
        $data = array(
            'details' => $this->details,
        );

        return $data;
    }

    protected function getEthDist()
    {
        return $this->ethDist;
    }

    protected function getFamilyDist()
    {
        return $this->familyDist;
    }

    protected function getFreqDist()
    {
        return $this->freqDist;
    }

    protected function getNewByType()
    {
        return $this->newByType;
    }

    protected function getNewMembers()
    {
        return $this->newMembers;
    }

    protected function getNewHouseholds()
    {
        return $this->newHouseholds;
    }

    protected function getResidency()
    {
        return $this->residency;
    }

    protected function getTotalHouseholds()
    {
        return $this->totalHouseholds;
    }

    protected function getTotalIndividuals()
    {
        return $this->totalIndividuals;
    }

    protected function getUniqHouseholds()
    {
        return $this->uniqHouseholds;
    }

    protected function getUniqIndividuals()
    {
        return $this->uniqIndividuals;
    }

    protected function getUniqNewIndividuals()
    {
        return $this->uniqNewIndividuals;
    }

    protected function getUniqServed()
    {
        return $this->uniqServed;
    }

    private function setNewByType($criterion)
    {
        //get new households for specific report type
        $queryNewType = $this->em->createQuery('SELECT COUNT(DISTINCT c.household) NewByType FROM TruckeeProjectmanaBundle:TempContact c '
                . $criterion['newWhereClause'] . " AND c.first = true")
            ->setParameters($criterion['parameters'])
            ->getSingleResult();

        $this->newByType = $queryNewType;
    }

    private function setNewHouseholds()
    {
        //get new households for specific report type
        $sql = 'select count(distinct household_id) NewHouseholds from temp_contact where `first` = 1';
        $qb = $this->em->createQuery('SELECT COUNT(DISTINCT c.household) NewHouseholds FROM TruckeeProjectmanaBundle:TempContact c '
                . 'WHERE c.first = true')
            ->getSingleResult();

        $this->newHouseholds = $qb;
    }

    private function setNewMembers($criterion)
    {
        //all new households
        $queryNewMembers = $this->em->createQuery('SELECT COUNT(m) NewMembers FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $criterion['newWhereClause'] . " AND c.first = true")
            ->setParameters($criterion['parameters'])
            ->getSingleResult();

        $this->newMembers = $queryNewMembers;
    }

    private function setTotalHouseholds()
    {
        $qb = $this->em->createQuery('SELECT COUNT(c.household) THS FROM TruckeeProjectmanaBundle:TempContact c ')
            ->getSingleResult();

        $this->totalHouseholds = $qb;
    }

    private function setUniqNewIndividuals()
    {
        //unique new participants
        $qb = $this->em->createQuery('SELECT COUNT(m) UNI FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . 'WHERE c.first = true')
            ->getSingleResult();

        $this->uniqNewIndividuals = $qb;
    }

    private function setUniqHouseholds()
    {
        $qb = $this->em->createQuery('SELECT COUNT(h) UHS FROM TruckeeProjectmanaBundle:TempHousehold h')
            ->getSingleResult();

        $this->uniqHouseholds = $qb;
    }

    private function setResidency()
    {
        $resArray = ['< 1 month', '1 mo - 2 yrs', '>=2 yrs'];
        $qb = $this->em->createQuery('SELECT SUM(h.size) FROM TruckeeProjectmanaBundle:TempHousehold h '
            . 'WHERE h.res = :res');
        foreach ($resArray as $res) {
            $n = $qb->setParameter('res', $res)->getSingleScalarResult();
            $residency[$res] = (null === $n) ? 0 : $n;
        }

        $this->residency = $residency;
    }

    private function setFamilyDist()
    {
        $familyArray = ['Single', 'Two', 'Three', 'Four', 'Five', 'Six or more'];
        $qb = $this->em->createQuery('SELECT COUNT(h.size) FROM TruckeeProjectmanaBundle:TempHousehold h '
            . 'WHERE h.sizeText = :size');
        foreach ($familyArray as $size) {
            $n = $qb->setParameter('size', $size)->getSingleScalarResult();
            $familyDist[$size] = (null === $n) ? 0 : $n;
        }

        $this->familyDist = $familyDist;
    }

    private function setFreqDist()
    {
        $frequency = ['1x' => 0, '2x' => 0, '3x' => 0, '4x' => 0];
        $qbSizes = $this->em->createQuery('SELECT h.id, h.size S FROM TruckeeProjectmanaBundle:TempHousehold h')->getResult();
        foreach ($qbSizes as $row) {
            $sizes[$row['id']] = $row['S'];
        }
        $qbFreqs = $this->em->createQuery('SELECT c.household, COUNT(c) N FROM TruckeeProjectmanaBundle:TempContact c '
                . 'GROUP BY c.household')->getResult();
        foreach ($qbFreqs as $freq) {
            $household = $freq['household'];
            $size = $sizes[$household];
            switch ($freq['N']) {
                case 1:
                    $frequency['1x'] += $size;
                    break;
                case 2:
                    $frequency['2x'] += $size;
                    break;
                case 3:
                    $frequency['3x'] += $size;
                    break;
                default:
                    $frequency['4x'] += $size;
                    break;
            }
        }

        $this->freqDist = $frequency;
    }

    private function setEthDist()
    {
        $qb = $this->em->createQuery('SELECT e FROM TruckeeProjectmanaBundle:Ethnicity e')->getResult();
        foreach ($qb as $row) {
            $queryEth = $this->em->createQuery('SELECT COUNT(m) N FROM TruckeeProjectmanaBundle:TempMember m '
                    . 'JOIN TruckeeProjectmanaBundle:TempHousehold h WITH m.household = h '
                    . 'WHERE (m <> h.head OR (m = h.head AND m.age IS NOT NULL)) AND m.ethnicity = :eth')
                ->setParameter('eth', $row)
                ->getSingleResult();
            $ethDist[$row->getEthnicity()] = $queryEth['N'];
        }

        $this->ethDist = $ethDist;
    }

    private function setAgeGenderDist()
    {
        $ageDist = ['Under 6' => 0, '6 - 18' => 0, '19 - 59' => 0, '60+' => 0];
        $ageGenderDist = ['FC' => 0, 'MC' => 0, 'FA' => 0, 'MA' => 0];
        $qb = $this->em->createQuery('SELECT m.age, m.sex FROM TruckeeProjectmanaBundle:TempMember m')->getResult();
        foreach ($qb as $row) {
            switch (true) {
                case $row['age'] < 6 && $row['sex'] == 'Female':
                    $ageDist['Under 6'] ++;
                    $ageGenderDist['FC'] ++;
                    break;
                case $row['age'] < 19 && $row['sex'] == 'Female':
                    $ageDist['6 - 18'] ++;
                    $ageGenderDist['FC'] ++;
                    break;
                case $row['age'] < 59 && $row['sex'] == 'Female':
                    $ageDist['19 - 59'] ++;
                    $ageGenderDist['FA'] ++;
                    break;
                case $row['age'] >= 60 && $row['sex'] == 'Female':
                    $ageDist['60+'] ++;
                    $ageGenderDist['FA'] ++;
                    break;
                case $row['age'] < 6 && $row['sex'] == 'Male':
                    $ageDist['Under 6'] ++;
                    $ageGenderDist['MC'] ++;
                    break;
                case $row['age'] < 19 && $row['sex'] == 'Male':
                    $ageDist['6 - 18'] ++;
                    $ageGenderDist['MC'] ++;
                    break;
                case $row['age'] < 59 && $row['sex'] == 'Male':
                    $ageDist['19 - 59'] ++;
                    $ageGenderDist['MA'] ++;
                    break;
                case $row['age'] >= 60 && $row['sex'] == 'Male':
                    $ageDist['60+'] ++;
                    $ageGenderDist['MA'] ++;
                    break;

                default:
                    break;
            }
        }

        $this->ageDist = $ageDist;
        $this->ageGenderDist = $ageGenderDist;
    }

    private function setUniqIndividuals()
    {
        //unique members
        $qb = $this->em->createQuery('SELECT SUM(h.size) UIS FROM TruckeeProjectmanaBundle:TempHousehold h')->getSingleResult();
        $this->uniqIndividuals = $qb;
    }

    private function setTotalIndividuals()
    {
        $qb = $this->em->createQuery("SELECT SUM(h.size) TIS FROM TruckeeProjectmanaBundle:TempHousehold h "
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH c.household = h')->getSingleResult();
        $this->totalIndividuals = $qb;
    }

    public function getCountyStats()
    {
        $joinPhrase = 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty GROUP BY cty.county';
        $db = $this->conn;
        $UISData = $this->em->createQuery('SELECT cty.county, COUNT(DISTINCT m) UIS FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $joinPhrase)->getResult();

        $UHSData = $this->em->createQuery('SELECT cty.county, COUNT(DISTINCT c.household) UHS FROM TruckeeProjectmanaBundle:TempContact c '
                . $joinPhrase)->getResult();


        $TISData = $this->em->createQuery('SELECT cty.county, COUNT(m) TIS FROM TruckeeProjectmanaBundle:TempMember m '
                . 'JOIN TruckeeProjectmanaBundle:TempContact c WITH m.household = c.household '
                . $joinPhrase)->getResult();

        $THSData = $this->em->createQuery('SELECT cty.county, COUNT(c.household) THS FROM TruckeeProjectmanaBundle:TempContact c '
                . $joinPhrase)->getResult();

        $totals['UISTotal'] = 0;
        $totals['UHSTotal'] = 0;
        $totals['TISTotal'] = 0;
        $totals['THSTotal'] = 0;

        if (empty($UISData)) {
            $sqlCounties = 'select county from county where enabled = 1 order by county asc';
            $counties = $db->query($sqlCounties)->fetchAll();
            $categories = ['UIS', 'TIS', 'UHS', 'THS'];
            foreach ($categories as $category) {
                $varname = $category . 'Data';
                foreach ($counties as $county) {
                    ${$varname}[] = array('county' => $county['county'], $category => 0);
                }
            }
        }
        foreach ($UISData as $array) {
            $totals['UIS'][$array['county']] = $array['UIS'];
            $totals['UISTotal'] += $array['UIS'];
        }
        foreach ($UHSData as $array) {
            $totals['UHS'][$array['county']] = $array['UHS'];
            $totals['UHSTotal'] += $array['UHS'];
        }
        foreach ($TISData as $array) {
            $totals['TIS'][$array['county']] = $array['TIS'];
            $totals['TISTotal'] += $array['TIS'];
        }
        foreach ($THSData as $array) {
            $totals['THS'][$array['county']] = $array['THS'];
            $totals['THSTotal'] += $array['THS'];
        }

        $data = [];
        foreach ($UISData as $array) {
            $data[$array['county']]['UIS'] = $array['UIS'];
            $data[$array['county']]['UISPCT'] = (0 < $totals['UISTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['UIS'] / $totals['UISTotal'])
                ) . '%' : 0;
        }
        foreach ($UHSData as $array) {
            $data[$array['county']]['UHS'] = $array['UHS'];
            $data[$array['county']]['UHSPCT'] = (0 < $totals['UHSTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['UHS'] / $totals['UHSTotal'])
                ) . '%' : 0;
        }
        foreach ($TISData as $array) {
            $data[$array['county']]['TIS'] = $array['TIS'];
            $data[$array['county']]['TISPCT'] = (0 < $totals['TISTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['TIS'] / $totals['TISTotal'])
                ) . '%' : 0;
        }
        foreach ($THSData as $array) {
            $data[$array['county']]['THS'] = $array['THS'];
            $data[$array['county']]['THSPCT'] = (0 < $totals['THSTotal']) ? sprintf(
                    '%01.1f',
                100 * ($array['THS'] / $totals['THSTotal'])
                ) . '%' : 0;
        }

        return $data;
    }

    /**
     * Set detail statistics.
     *
     * details creates an array $countyStats[county][type][category]
     * where type = contact description, category is Unique or Total
     * Individuals or Households.
     *
     * @param array $criteria
     */
    public function setDetails($criteria)
    {
        $this->setCriteria($criteria, 'details');
        $this->makeTempTables($this->commonCriteria);
        $countyDescQuery = $this->em->createQuery('SELECT DISTINCT cty.county, d.contactDesc FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c.contactType = d '
                . 'ORDER BY cty.county, d.contactDesc')->getResult();

        $countystats = [];
        foreach ($countyDescQuery as $countyDesc) {
            $county = $countyDesc['county'];
            $desc = $countyDesc['contactDesc'];
            $countystats[$county][$desc]['uniqInd'] = 0;
            $countystats[$county][$desc]['uniqHouse'] = 0;
            $countystats[$county][$desc]['totalInd'] = 0;
            $countystats[$county][$desc]['totalHouse'] = 0;
        }

        $householdSizeQuery = $this->em->createQuery('SELECT h.id, h.size FROM TruckeeProjectmanaBundle:TempHousehold h')->getResult();
        foreach ($householdSizeQuery as $row) {
            $householdSizeArray[$row['id']] = $row['size'];
        }
        $distinctCountyDescIndividualQuery = $this->em->createQuery('SELECT DISTINCT cty.county, d.contactDesc, c.household FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c.contactType = d')->getResult();
        $countyDescIndividualQuery = $this->em->createQuery('SELECT cty.county, d.contactDesc, c.household FROM TruckeeProjectmanaBundle:TempContact c '
                . 'JOIN TruckeeProjectmanaBundle:County cty WITH c.county = cty '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c.contactType = d')->getResult();
        foreach ($distinctCountyDescIndividualQuery as $distinctCountyDescIndividual) {
            $houshold = $distinctCountyDescIndividual['household'];
            $size = $householdSizeArray[$houshold];
            $cty = $distinctCountyDescIndividual['county'];
            $cDesc = $distinctCountyDescIndividual['contactDesc'];
            $countystats[$cty][$cDesc]['uniqInd'] += $size;
            $countystats[$cty][$cDesc]['uniqHouse'] ++;
        }
        foreach ($countyDescIndividualQuery as $countyDescIndividual) {
            $houshold = $countyDescIndividual['household'];
            $size = $householdSizeArray[$houshold];
            $cty = $countyDescIndividual['county'];
            $cDesc = $countyDescIndividual['contactDesc'];
            $countystats[$cty][$cDesc]['totalInd'] += $size;
            $countystats[$cty][$cDesc]['totalHouse'] ++;
        }
        $details = array(
            'details' => $countystats,
        );

        $this->details = $details;
    }

    public function getStats()
    {
        $data = array();
        $data['statistics'] = $this->statistics;

        return $data;
    }

    /**
     * Get set of households with multiple same-date contacts
     *
     * @param type $criteria
     *
     * @return array
     */
    public function getMultiContacts($criteria)
    {
        $this->setCriteria($criteria);
        $qb = $this->em->createQuery('SELECT DISTINCT IDENTITY(c1.household) id, m.fname, m.sname, '
                . 'r.center, c1.contactDate, d.contactDesc FROM TruckeeProjectmanaBundle:Contact c1 '
                . 'JOIN TruckeeProjectmanaBundle:Contact c2 WITH c1.household = c2.household '
                . 'JOIN TruckeeProjectmanaBundle:Household h WITH c1.household = h '
                . 'JOIN TruckeeProjectmanaBundle:Member m WITH m = h.head '
                . 'JOIN TruckeeProjectmanaBundle:Center r WITH c1.center = r '
                . 'JOIN TruckeeProjectmanaBundle:ContactDesc d WITH c1.contactDesc = d '
                . 'WHERE c1.contactDate = c2.contactDate '
                . 'AND c1 <> c2 '
                . 'AND c1.contactDate >= :startDate AND c1.contactDate <= :endDate')
            ->setParameters(['startDate' => $criteria['startDate'], 'endDate' => $criteria['endDate']])
            ->getResult();

        return $qb;
    }

    /**
     * Get site distributions by month for index page chart
     *
     * @return string
     */
    public function getDistsFYToDate()
    {
        $fy = $this->getFY();
        $month = date_format(new \DateTime(), 'n');
        $chart['fy'] = $fy;
        $fy_months = array_merge(range(7, 12), range(1, 6));
        $i = 0;
        for ($i = 0; $fy_months[$i] != $month; ++$i) {
            $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "', ";
        }
        $chart['categories'][] = "'" . date_format(new \DateTime('2000-' . $fy_months[$i] . '-01'), 'F') . "'";
        $sqlSites = 'SELECT center FROM center WHERE enabled = TRUE ORDER BY center ASC';
        $siteQuery = $this->conn->fetchAll($sqlSites);

        $series = '[';
        foreach ($siteQuery as $siteArray) {
            $site = $siteArray['center'];
            $seriesString = "{name:'$site', data:[";
            $qb = $this->em->createQuery('SELECT MONTHNAME(c.contactDate) Mo, COUNT(DISTINCT c.household) N FROM TruckeeProjectmanaBundle:Contact c '
                    . 'JOIN TruckeeProjectmanaBundle:Center r WITH c.center = r '
                    . 'WHERE r.center = :site AND FY(c.contactDate) = :fy '
                    . 'GROUP BY Mo ORDER BY c.contactDate')
                ->setParameters(['site' => $site, 'fy' => $fy])
                ->getResult();
            foreach ($qb as $array) {
                $seriesString .= $array['N'] . ',';
            }
            $series .= $seriesString . ']}, ';
        }
        $chart['series'] = $series . ']';

        return $chart;
    }

    private function getFY()
    {
        $year = date_format(new \DateTime(), 'Y');
        $month = date_format(new \DateTime(), 'n');
        $fy = ($month < 7) ? $year : $year + 1;

        return $fy;
    }
}
