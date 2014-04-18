<?php

namespace Mana\ClientBundle\Utilities;

use Doctrine\DBAL\Connection;

/**
 * Project MANA statistics
 */
class Reports {

    private $conn;
    private $_where;    //single value where clause
    private $ageDist;
    private $ageGenderDist;
    private $criteria;
    private $details;
    private $end;
    private $ethDist;
    private $familyDist;
    private $freqDist;
    private $newByType;
    private $newMembers;
    private $newHouseholds;
    private $residency;
    private $specs;
    private $start;
    private $statistics;
    private $totalHouseholds;
    private $totalIndividuals;
    private $uniqHouseholds;
    private $uniqIndividuals;
    private $uniqNewIndividuals;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    private function setCriteria($criteria, $source = null) {
        //$start, $end, $vid = 0, $cty_id = 0, $rid = 0
        //$vid -> contact_type_id; $cty_id -> county_id; $rid -> center_id
        $startMonth = $criteria['startMonth'];
        $startYear = $criteria['startYear'];
        $startDate = new \DateTime($startMonth . '/01/' . $startYear);
        $startText = $startDate->format('Y-m-d');
        $endMonth = $criteria['endMonth'];
        $endYear = $criteria['endYear'];
        $endDate = new \DateTime($endMonth . '/01/' . $endYear);
        $endText = $endDate->format('Y-m-t');

        $contact_type_id = (empty($criteria['contact_type_id'])) ? 0 : $criteria['contact_type_id'];
        $center_id = (empty($criteria['center_id'])) ? 0 : $criteria['center_id'];
        $county_id = (empty($criteria['county_id'])) ? 0 : $criteria['county_id'];

        $this->start = $startText;
        $this->end = $endText;
        $incoming = array('contact_type_id' => $contact_type_id, 'county_id' => $county_id, 'center_id' => $center_id);
        $where = " where contact_date >= '$startText' and contact_date < '$endText' ";
        $this->specs = array(
            'startMonth' => $startMonth,
            'startYear' => $startYear,
            'endMonth' => $endMonth,
            'endYear' => $endYear,
            'startDate' => $startDate,
            'endDate' => $endDate,
        );
        $options = array('contact_type_id', 'county_id', 'center_id');
        foreach ($options as $opt) {
            if ($incoming[$opt]) {
                $where .= " and $opt = $incoming[$opt]";
            }
        }
        //set criteria for common statistics
        $this->_where = $where;
        //set criteria for multiple counties
        $this->criteria = array();
        if (!$incoming['county_id'] && !$incoming['center_id']) {
            $sql = 'select id from county where enabled=1';
            $stmt = $this->conn->query($sql);
            while ($row = $stmt->fetch()) {
                array_push($this->criteria, $where . " and county_id = $row[id]");
            }
        } else {
            array_push($this->criteria, $where);
        }

    }

    public function dataExist() {
        $sql = 'select count(*) N from contacts' . $this->_where;
        $data = $this->conn->fetchAssoc($sql);
        return $data['N'];
    }

    public function setStats($criteria) {

        $this->setCriteria($criteria);
        //fill tables for common statistics
        $this->_makeTempTables($this->_where);

        //calculate common statistics
        $this->_setNewByType($this->_where);
        $this->_setNewMembers($this->_where);
        $this->_setNewHouseholds();
        $this->_setUniqNewIndividuals();
        $this->_setUniqHouseholds();
        $this->_setResidency();
        $this->_setFamilyDist();
        $this->_setFreqDist();
        $this->_setEthDist();
        $this->_setAgeDist();
        $this->_setAgeGenderDist();
        $this->_setUniqIndividuals();
        $this->_setTotalIndividuals();
        $this->_setTotalHouseholds();
        $statistics = array();
        foreach ($this->ageDist as $key => $value) {
            $item = $this->ageDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->residency as $key => $value) {
            $item = $this->residency[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->familyDist as $key => $value) {
            $item = $this->familyDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->freqDist as $key => $value) {
            $item = $this->freqDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->ethDist as $key => $value) {
            $item = $this->ethDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }
        foreach ($this->ageGenderDist as $key => $value) {
            $item = $this->ageGenderDist[$key];
            $statistics[$key] = (!empty($item)) ? $item : 0;
        }

        $uniqNewInd = $this->getUniqNewIndividuals();
        $item = $uniqNewInd['UNI'];
        $statistics['Unique New Individuals'] = (!empty($item)) ? $item : 0;

        $ti = $this->getTotalIndividuals();
        $item = $ti['IS'];
        $statistics['IS'] = (!empty($item)) ? $item : 0;

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

    private function _makeTempTables($criteria) {
        /*
         * establish tables for basis of calculations
         */
        $sql = 'delete from temp_contact';
        $this->conn->exec($sql);
        $sql = 'insert into temp_contact
            (contact_type_id, household_id, contact_date, first, center_id, county_id)
            select contact_type_id, household_id, contact_date, first, center_id, county_id
            from contact '
                . $criteria;
        $this->conn->exec($sql);

        $sql = 'delete from temp_member';
        $this->conn->exec($sql);

        $sql = "INSERT INTO temp_member
            (id, household_id, sex, age, ethnicity_id)
            select distinct m.id, m.household_id, sex,
            (year(now()) - year(dob) - (concat(month(now()),'-01') < right(dob,5))) as 'age',
            ethnicity_id
            from member m
            join temp_contact ct on m.household_id = ct.household_id where 
            (exclude_date > '$this->start' or exclude_date is null) and (dob < '$this->start' or dob is null)";
        $n = $this->conn->exec($sql);

        $sql = 'delete from temp_household';
        $this->conn->exec($sql);
        $sql = "INSERT INTO temp_household
            (id, hoh_id, res, size, date_added)
            select distinct h.id, hoh_id,
            12*(year(now()) - cast(arrivalYear as signed)) + (month(now()) - cast(arrivalMonth as signed)) as 'res',
            size, date_added from household h 
			join 
			(select household_id, if(count(dob)=0,1,count(dob)) size  from member 
            group by household_id having count(dob) is not null) B
			on h.id = B.household_id
            join temp_contact c on c.household_id = h.id";
        $n = $this->conn->exec($sql);
    }

    protected function getAgeDist() {
        return $this->ageDist;
    }

    protected function getAgeGenderDist() {
        return $this->ageGenderDist;
    }

    public function getDetails() {
        $data = array(
            'details' => $this->details,
            'specs' => $this->specs,
        );
        return $data;
    }

    protected function getEthDist() {
        return $this->ethDist;
    }

    protected function getFamilyDist() {
        return $this->familyDist;
    }

    protected function getFreqDist() {
        return $this->freqDist;
    }

    protected function getNewByType() {
        return $this->newByType;
    }

    protected function getNewMembers() {
        return $this->newMembers;
    }

    protected function getNewHouseholds() {
        return $this->newHouseholds;
    }

    protected function getResidency() {
        return $this->residency;
    }

    protected function getTotalHouseholds() {
        return $this->totalHouseholds;
    }

    protected function getTotalIndividuals() {
        return $this->totalIndividuals;
    }

    protected function getUniqHouseholds() {
        return $this->uniqHouseholds;
    }

    protected function getUniqIndividuals() {
        return $this->uniqIndividuals;
    }

    protected function getUniqNewIndividuals() {
        return $this->uniqNewIndividuals;
    }

    protected function getUniqServed() {
        return $this->uniqServed;
    }

    private function _setNewByType($criterion) {
        //get new households for specific report type
        $sql = 'select count(distinct household_id) NewByType from temp_contact' .
                $criterion . " and `first` = 1";
        $this->newByType = $this->conn->fetchAssoc($sql);
    }

    private function _setNewHouseholds() {
        //get new households for specific report type
        $sql = 'select count(distinct household_id) NewHouseholds from temp_contact where `first` = 1';
        $this->newHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function _setNewMembers($criterion) {
        //all new households
        $sql = "select count(*) NewMembers from temp_member m
            join temp_contact c on m.household_id = c.household_id" .
                $criterion .
                " and `first` = 1";
        $this->newMembers = $this->conn->fetchAssoc($sql);
    }

    private function _setTotalHouseholds() {
        $sql = "select count(household_id) 'THS' from temp_contact";
        $this->totalHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function _setUniqNewIndividuals() {
        //unique new participants
        $sql = "select count(*) 'UNI' from temp_member m
            join temp_contact c on m.household_id = c.household_id
            and first = 1";
        $this->uniqNewIndividuals = $this->conn->fetchAssoc($sql);
    }

    private function _setUniqHouseholds() {
        $sql = "select count(*) 'UHS' from temp_household";
        $this->uniqHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function _setResidency() {

        $sql = "select  sum(if(res<1,size,0)) as '< 1 month',
            sum(if(1<=res and res<=24,size,0)) as '1 mo - 2 yrs',
            sum(if(24<res,size,0)) as '>=2 yrs' from (
            select m.household_id, res, size from temp_member m
            join temp_household h on m.household_id = h.id group by m.household_id) A";
        $res = $this->conn->fetchAssoc($sql);
        $this->residency = $res;
    }

    private function _setFamilyDist() {
        $sql = "select sum(if(size=1,1,0)) as 'Single', sum(if(size=2,1,0)) as 'Two',
            sum(if(size=3,1,0)) as 'Three', sum(if(size=4,1,0)) as 'Four',
            sum(if(size=5,1,0)) as 'Five', sum(if(size>5,1,0)) as 'Six or more' from (
            select size from temp_household
            ) A";
        $this->familyDist = $this->conn->fetchAssoc($sql);
    }

    private function _setFreqDist() {
        $sql = "select sum(if(freq=1,size,0)) '1x', sum(if(freq=2,size,0)) '2x', sum(if(freq=3,size,0)) '3x', sum(if(4<=freq,size,0)) '4x' from (
            (select c.household_id, count(*) freq from temp_contact c
            group by c.household_id) Freqs
            join
            (select id, size from temp_household ) Sizes
            on Freqs.household_id = Sizes.id)";
        $this->freqDist = $this->conn->fetchAssoc($sql);
    }

    private function _setEthDist() {
        $ethDist = array();
        $sql = 'select ethnicity, count(ethnicity_id) N from ethnicity e
            left outer join temp_member m on e.id = m.ethnicity_id
            left outer join temp_household h on m.household_id = h.id
            where m.id <> h.hoh_id or
            (m.id = h.hoh_id and m.age is not null) or m.ethnicity_id is null
            group by ethnicity';
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch()) {
            $ethDist[$row['ethnicity']] = $row['N'];
        }
        $this->ethDist = $ethDist;
    }

    private function _setAgeDist() {
        $sql = "select sum(if(age<6,1,0)) as 'Under 6', sum(if(6<=age and age<19,1,0)) as '6 - 18',
            sum(if(19<=age and age<60,1,0)) as '19 - 59', sum(if(age>=60,1,0)) as '60+' from
            temp_member m where m.age is not null";
        $age = $this->conn->fetchAssoc($sql);

        $this->ageDist = $age;
    }

    private function _setAgeGenderDist() {
        $sql = "select sum(if(age<18 and sex='Female',1,0)) as 'FC',
            sum(if(age<18 and sex='Male',1,0)) as 'MC',
            sum(if(age>=18 and sex='Female',1,0)) as 'FA',
            sum(if(age>=18 and sex='Male',1,0)) as 'MA' from
            temp_member m
            where m.age is not null";
        $ageGenderDist = $this->conn->fetchAssoc($sql);

        $this->ageGenderDist = $ageGenderDist;
    }

    private function _setUniqIndividuals() {
        //unique members
        $sql = 'select sum(size) UIS from temp_household';
        $this->uniqIndividuals = $this->conn->fetchAssoc($sql);
    }

    private function _setTotalIndividuals() {
        $sql = "select sum(freq*size) 'IS' from (
            (select c.household_id, count(*) freq
            from temp_contact c
            group by c.household_id) Freqs
            join
            (select id, size from temp_household ) Sizes
            on Freqs.household_id = Sizes.id)";
        $this->totalIndividuals = $this->conn->fetchAssoc($sql);
    }

    public function getCountyStats() {
        $subtotal = array();
        foreach ($this->criteria as $criteria) {
            //create tables for calculations
            $this->_makeTempTables($criteria);

            //get or make value for county id
//            $county_id = (strpos($criteria, 'county_id')) ? substr($criteria, strpos($criteria, 'county_id') + 12, 1) : 0;
            $county_id = (strpos($criteria, 'county_id')) ? substr($criteria, -2) : 0;
            
            $countyArr = $this->conn->fetchAssoc("select county from county where id=$county_id");
            $county = $countyArr['county'];
            
            $this->_setUniqIndividuals();
            $ui = $this->getUniqIndividuals();
            $subtotal[$county]['UIS'] = (!empty($ui['UIS'])) ? $ui['UIS'] : 0;
            $this->_setTotalIndividuals();
            $ti = $this->getTotalIndividuals();
            $subtotal[$county]['IS'] = (!empty($ti['IS'])) ? $ti['IS'] : 0;
            $this->_setUniqHouseholds($criteria);
            $uh = $this->getUniqHouseholds();
            $subtotal[$county]['UHS'] = (!empty($uh['UHS'])) ? $uh['UHS'] : 0;
            $this->_setTotalHouseholds($criteria);
            $th = $this->getTotalHouseholds();
            $subtotal[$county]['THS'] = (!empty($th['THS'])) ? $th['THS'] : 0;
        }
        return $subtotal;
    }

    public function setDetails($criteria) {
        //details creates an array $countyStats[county][type][category]
        //where type = contact description, category is Unique or Total
        //Individuals or Households

        $this->setCriteria($criteria, 'details');
        $this->_makeTempTables($this->_where);

        $countystats = array();
        $sql = 'select county, contact_desc, sum(N) UIS from (
            select distinct county, contact_desc, t1.household_id, N from temp_contact t1
            join county t2 on t2.id = t1.county_id
            join contact_type t3 on t3.id = t1.contact_type_id
            join (select m.household_id, if(count(m.household_id)>1 and h.date_added is null,count(m.household_id)-1,count(m.household_id))  N
            from temp_member m
            join temp_household h on m.household_id = h.id
            group by m.household_id) t4
            on t4.household_id = t1.household_id
            order  by county, contact_desc) t5
            group by county, contact_desc';
        $resultUIS = $this->conn->fetchAll($sql);
        foreach ($resultUIS as $value) {
            $countystats[$value['county']][$value['contact_desc']]['uniqInd'] = $value['UIS'];
        }

        $sql = "select county, contact_desc, sum(N) as 'IS' from (
            select county, contact_desc, t1.household_id, N from temp_contact t1
            join county t2 on t2.id = t1.county_id
            join contact_type t3 on t3.id = t1.contact_type_id
            join (select m.household_id, if(count(m.household_id)>1 and h.date_added is null,count(m.household_id)-1,count(m.household_id))  N
            from temp_member m
            join temp_household h on m.household_id = h.id
            group by m.household_id) t4
            on t4.household_id = t1.household_id
            order  by county, contact_desc) t5
            group by county, contact_desc";
        $resultIS = $this->conn->fetchAll($sql);
        foreach ($resultIS as $value) {
            $countystats[$value['county']][$value['contact_desc']]['totalInd'] = $value['IS'];
        }

        $sql = 'select county, contact_desc, count(*) UHS from temp_household t1
			join (select distinct county, contact_desc, household_id from temp_contact t1
			join county t2 on t2.id = t1.county_id
			join contact_type t3 on t3.id = t1.contact_type_id) t2
			on t2.household_id = t1.id
			group by county, contact_desc';
        $resultUHS = $this->conn->fetchAll($sql);
        foreach ($resultUHS as $value) {
            $countystats[$value['county']][$value['contact_desc']]['uniqHouse'] = $value['UHS'];
        }

        $sql = 'select county, contact_desc, count(household_id) THS from temp_contact t1
			join county t2 on t2.id = t1.county_id
			join contact_type t3 on t3.id = t1.contact_type_id
			group by county, contact_desc';
        $resultThs = $this->conn->fetchAll($sql);
        foreach ($resultThs as $value) {
            $countystats[$value['county']][$value['contact_desc']]['totalHouse'] = $value['THS'];
        }
        $details = array(
            'details' => $countystats,
            'specs' => $this->specs,
            );
        $this->details = $details;
    }

    public function getStats() {
        $data = array();
        $data['statistics'] = $this->statistics;
        $data['specs'] = $this->specs;
        return $data;
    }

    /**
     * calculate county percentages
     * @param type $stats
     * @param type $counties
     */
    public function getCountyPcts($statistics, $counties, $ctyStats) {
        foreach ($counties as $county) {
            $county = $county->getCounty();
            $pct_IS = ($statistics['IS']) ? round((100 * $ctyStats[$county]['IS'] / $statistics['IS']), 1) : '-';
            $pct_UIS = ($statistics['UIS']) ? round((100 * $ctyStats[$county]['UIS'] / $statistics['UIS']), 1) : '-';
            $pct_THS = ($statistics['THS']) ? round((100 * $ctyStats[$county]['THS'] / $statistics['THS']), 1) : '-';
            $pct_UHS = ($statistics['UHS']) ? round((100 * $ctyStats[$county]['UHS'] / $statistics['UHS']), 1) : '-';
            $countypct[$county] = array(
                'IS' => $pct_IS,
                'UIS' => $pct_UIS,
                'THS' => $pct_THS,
                'UHS' => $pct_UHS);
        }
        return $countypct;
    }

    public function getMultiContacts($criteria) {
        $this->setCriteria($criteria);
        $sql = "select distinct c1.household_id id, m.fname, m.sname, center, 
            date_format(c1.contact_date,'%m/%d/%Y') 'contact_date', contact_desc from contact c1
            join contact c2 on c1.household_id = c2.household_id
            join household h on c1.household_id = h.id
            join member m on m.id = h.hoh_id
            join center t1 on c1.center_id = t1.id
            join contact_type t2 on c1.contact_type_id = t2.id
            where c1.contact_date = c2.contact_date
            and c1.id <> c2.id
            and c1.contact_date between '$this->start' and '$this->end' 
            and c2.contact_date between '$this->start' and '$this->end' ";
        $multis = $this->conn->fetchAll($sql);
        return $multis;
    }
}