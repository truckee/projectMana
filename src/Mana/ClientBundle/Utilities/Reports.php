<?php

namespace Mana\ClientBundle\Utilities;

use Doctrine\DBAL\Connection;

/**
 * Project MANA statistics.
 */
class Reports
{

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

    private function setCriteria($criteria) {
        $this->start = $criteria['startDate'];
        $this->end = $criteria['endDate'];
        $incoming = array(
            'contact_type_id' => (!empty($criteria['contact_type'])) ? $criteria['contact_type'] : '',
            'county_id' => (!empty($criteria['county'])) ? $criteria['county'] : '',
            'center_id' => (!empty($criteria['center'])) ? $criteria['center'] : ''
        );
        $where = " where contact_date >= '$this->start' and contact_date <= '$this->end' ";

        $options = array('contact_type_id', 'county_id', 'center_id');
        foreach ($options as $opt) {
            if ('' !== $incoming[$opt]) {
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

    public function setStats($criteria) {
        $this->setCriteria($criteria);
        //fill tables for common statistics
        $this->makeTempTables($this->_where);

        //calculate common statistics
        $this->setNewByType($this->_where);
        $this->setNewMembers($this->_where);
        $this->setNewHouseholds();
        $this->setUniqNewIndividuals();
        $this->setUniqHouseholds();
        $this->setResidency();
        $this->setFamilyDist();
        $this->setFreqDist();
        $this->setEthDist();
        $this->setAgeDist();
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

    private function makeTempTables($criteria) {
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

        //note use of custom MySQL age() function
        $sql = "INSERT INTO temp_member
            (id, household_id, sex, age, ethnicity_id)
            select distinct m.id, m.household_id, sex,
            age(m.dob), ethnicity_id
            from member m
            join temp_contact ct on m.household_id = ct.household_id where
            (exclude_date > '$this->start' or exclude_date is null) and (dob < '$this->start' or dob is null)";
        $n = $this->conn->exec($sql);

        $sql = 'delete from temp_household';
        $this->conn->exec($sql);

        //note use of custom MySQL residency()), household_size() functions
        $sql = "INSERT INTO temp_household
            (id, hoh_id, res, size, date_added)
            select distinct h.id, hoh_id,
            residency(h.id),
            household_size(h.id, '$this->start'), date_added from household h
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

    private function setNewByType($criterion) {
        //get new households for specific report type
        $sql = 'select count(distinct household_id) NewByType from temp_contact' .
                $criterion . ' and `first` = 1';
        $this->newByType = $this->conn->fetchAssoc($sql);
    }

    private function setNewHouseholds() {
        //get new households for specific report type
        $sql = 'select count(distinct household_id) NewHouseholds from temp_contact where `first` = 1';
        $this->newHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function setNewMembers($criterion) {
        //all new households
        $sql = 'select count(*) NewMembers from temp_member m
            join temp_contact c on m.household_id = c.household_id' .
                $criterion .
                ' and `first` = 1';
        $this->newMembers = $this->conn->fetchAssoc($sql);
    }

    private function setTotalHouseholds() {
        $sql = "select count(household_id) 'THS' from temp_contact";
        $this->totalHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function setUniqNewIndividuals() {
        //unique new participants
        $sql = "select count(*) 'UNI' from temp_member m
            join temp_contact c on m.household_id = c.household_id
            and first = 1";
        $this->uniqNewIndividuals = $this->conn->fetchAssoc($sql);
    }

    private function setUniqHouseholds() {
        $sql = "select count(*) 'UHS' from temp_household";
        $this->uniqHouseholds = $this->conn->fetchAssoc($sql);
    }

    private function setResidency() {
        $sql = "select  sum(if(res<1,size,0)) as '< 1 month',
            sum(if(1<=res and res<=24,size,0)) as '1 mo - 2 yrs',
            sum(if(24<res,size,0)) as '>=2 yrs' from (
            select m.household_id, res, size from temp_member m
            join temp_household h on m.household_id = h.id group by m.household_id) A";
        $res = $this->conn->fetchAssoc($sql);
        $this->residency = $res;
    }

    private function setFamilyDist() {
        $sql = "select sum(if(size=1,1,0)) as 'Single', sum(if(size=2,1,0)) as 'Two',
            sum(if(size=3,1,0)) as 'Three', sum(if(size=4,1,0)) as 'Four',
            sum(if(size=5,1,0)) as 'Five', sum(if(size>5,1,0)) as 'Six or more' from (
            select size from temp_household
            ) A";
        $this->familyDist = $this->conn->fetchAssoc($sql);
    }

    private function setFreqDist() {
        $sql = "select sum(if(freq=1,size,0)) '1x', sum(if(freq=2,size,0)) '2x', sum(if(freq=3,size,0)) '3x', sum(if(4<=freq,size,0)) '4x' from (
            (select c.household_id, count(*) freq from temp_contact c
            group by c.household_id) Freqs
            join
            (select id, size from temp_household ) Sizes
            on Freqs.household_id = Sizes.id)";
        $this->freqDist = $this->conn->fetchAssoc($sql);
    }

    private function setEthDist() {
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

    private function setAgeDist() {
        $sql = "select sum(if(age<6,1,0)) as 'Under 6', sum(if(6<=age and age<19,1,0)) as '6 - 18',
            sum(if(19<=age and age<60,1,0)) as '19 - 59', sum(if(age>=60,1,0)) as '60+' from
            temp_member m where m.age is not null";
        $age = $this->conn->fetchAssoc($sql);

        $this->ageDist = $age;
    }

    private function setAgeGenderDist() {
        $sql = "select sum(if(age<18 and sex='Female',1,0)) as 'FC',
            sum(if(age<18 and sex='Male',1,0)) as 'MC',
            sum(if(age>=18 and sex='Female',1,0)) as 'FA',
            sum(if(age>=18 and sex='Male',1,0)) as 'MA' from
            temp_member m
            where m.age is not null";
        $ageGenderDist = $this->conn->fetchAssoc($sql);

        $this->ageGenderDist = $ageGenderDist;
    }

    private function setUniqIndividuals() {
        //unique members
        $sql = 'select sum(size) UIS from temp_household';
        $this->uniqIndividuals = $this->conn->fetchAssoc($sql);
    }

    private function setTotalIndividuals() {
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
        $db = $this->conn;
        $sqlUIS = 'select cty.county, count(distinct tm.id) as UIS from temp_member tm
join temp_contact tc on tm.household_id = tc.household_id
join county cty on cty.id = tc.county_id
group by cty.county';

        $sqlUHS = 'select cty.county, count(distinct tc.household_id) as UHS from temp_contact tc
join county cty on cty.id = tc.county_id
group by cty.county';

        $sqlTIS = 'select cty.county, count(tm.id) as TIS from temp_member tm
join temp_contact tc on tm.household_id = tc.household_id
join county cty on cty.id = tc.county_id
group by cty.county';

        $sqlTHS = 'select cty.county, count(tc.household_id) as THS from temp_contact tc
join county cty on cty.id = tc.county_id
group by cty.county';

        $UISData = $db->query($sqlUIS)->fetchAll();
        $UHSData = $db->query($sqlUHS)->fetchAll();
        $TISData = $db->query($sqlTIS)->fetchAll();
        $THSData = $db->query($sqlTHS)->fetchAll();

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
            $data[$array['county']]['UISPCT'] = (0 < $totals['UISTotal']) ? sprintf('%01.1f', 100 * ($array['UIS'] / $totals['UISTotal'])) . '%' : 0;
        }
        foreach ($UHSData as $array) {
            $data[$array['county']]['UHS'] = $array['UHS'];
            $data[$array['county']]['UHSPCT'] = (0 < $totals['UHSTotal']) ? sprintf('%01.1f', 100 * ($array['UHS'] / $totals['UHSTotal'])) . '%' : 0;
        }
        foreach ($TISData as $array) {
            $data[$array['county']]['TIS'] = $array['TIS'];
            $data[$array['county']]['TISPCT'] = (0 < $totals['TISTotal']) ? sprintf('%01.1f', 100 * ($array['TIS'] / $totals['TISTotal'])) . '%' : 0;
        }
        foreach ($THSData as $array) {
            $data[$array['county']]['THS'] = $array['THS'];
            $data[$array['county']]['THSPCT'] = (0 < $totals['THSTotal']) ? sprintf('%01.1f', 100 * ($array['THS'] / $totals['THSTotal'])) . '%' : 0;
        }

        return $data;
    }

    public function setDetails($criteria) {
        //details creates an array $countyStats[county][type][category]
        //where type = contact description, category is Unique or Total
        //Individuals or Households

        $this->setCriteria($criteria, 'details');
        $this->makeTempTables($this->_where);

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
//            'specs' => $this->specs,
        );
        $this->details = $details;
    }

    public function getStats() {
        $data = array();
        $data['statistics'] = $this->statistics;
//        $data['specs'] = $this->specs;

        return $data;
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

    public function getFiscalYearToDate() {
        $year = date_format(new \DateTime(), 'Y');
        $month = date_format(new \DateTime(), 'n');
        $fy = ($month < 7) ? $year : $year + 1;
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
            $sql = 'SELECT COUNT(DISTINCT c.household_id) N FROM contact c JOIN center r ON r.id=c.center_id WHERE r.center=';
            $sql .= "'$site'";
            $sql .= ' AND fy(c.contact_date) = ';
            $sql .= "'$fy'";
            $sql .= ' GROUP BY monthname(c.contact_date) ORDER BY c.contact_date';
            $query = $this->conn->fetchAll($sql);
            foreach ($query as $array) {
                $seriesString .= $array['N'] . ',';
            }
            $series .= $seriesString . ']}, ';
        }
        $chart['series'] = $series . ']';

        return $chart;
    }

}
