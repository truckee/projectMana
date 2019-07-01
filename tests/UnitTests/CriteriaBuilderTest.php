<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\UnitTests;

use App\Services\CriteriaBuilder;
use App\Entity\Center;
use App\Entity\Contactdesc;
use App\Entity\County;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

/**
 * Description of CriteriaBuilderTest
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class CriteriaBuilderTest extends TestCase {

    private $incoming;
    private $em;
    private $startDate;
    private $betweenStart;
    private $endDate;
    private $betweenWhereClause;
    private $betweenParameters;
    private $repo;

    public function setup(): void {
        $this->incoming = [
            'startMonth' => 5,
            'startYear' => 2019,
            'endMonth' => 5,
            'endYear' => 2019,
            'center' => '',
            'county' => '',
            'contactdesc' => '',
        ];
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->startDate = $this->betweenStart = new \DateTime('May 1, 2019');
        $this->endDate = new \DateTime('May 31, 2019');
        $this->betweenWhereClause = 'c.contactDate BETWEEN :startDate AND :endDate';
        $this->betweenParameters = ['startDate' => $this->startDate, 'endDate' => $this->endDate];
        $this->repo = $this->createMock(ObjectRepository::class);
    }

    public function testDetailsCriteria() {
        $builder = new CriteriaBuilder($this->em);
        $criteria = $builder->getDetailsCriteria($this->incoming);

        $this->assertEquals($this->betweenWhereClause, $criteria['betweenWhereClause']);
        $this->assertEquals($this->betweenParameters, $criteria['betweenParameters']);
        $this->assertEquals($this->startDate, $criteria['startParameters']['startDate']);
    }

    public function testGeneralCriteriaNoSiteNoCountyNoContactDes() {
        $builder = new CriteriaBuilder($this->em);
        $criteria = $builder->getGeneralCriteria($this->incoming);

        $this->assertEquals('1 = 1', $criteria['siteWhereClause']);
        $this->assertEquals([], $criteria['siteParameters']);
        $this->assertEquals('1 = 1', $criteria['contactWhereClause']);
        $this->assertEquals([], $criteria['contactParameters']);
    }

    public function testGeneralCriteriaSiteNoCountyNoContactDes() {
        $builder = new CriteriaBuilder($this->em);
        $this->incoming['center'] = 1;
        
        $center = new Center();
        
        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repo);
        $this->repo->expects($this->any())
            ->method('find')
            ->willReturn($center);
        
        $criteria = $builder->getGeneralCriteria($this->incoming);

        $this->assertEquals('c.center = :center', $criteria['siteWhereClause']);
        $this->assertEquals(['center' => $center], $criteria['siteParameters']);
        $this->assertEquals('1 = 1', $criteria['contactWhereClause']);
        $this->assertEquals([], $criteria['contactParameters']);
    }

    public function testGeneralCriteriaNoSiteCountyNoContactDes() {
        $builder = new CriteriaBuilder($this->em);
        $this->incoming['county'] = 1;
        
        $county = new County();
        
        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repo);
        $this->repo->expects($this->any())
            ->method('find')
            ->willReturn($county);
        
        $criteria = $builder->getGeneralCriteria($this->incoming);

        $this->assertEquals('c.county = :county', $criteria['siteWhereClause']);
        $this->assertEquals(['county' => $county], $criteria['siteParameters']);
        $this->assertEquals('1 = 1', $criteria['contactWhereClause']);
        $this->assertEquals([], $criteria['contactParameters']);
    }

    public function testGeneralCriteriaNoSiteNoCountyContactDesc() {
        $builder = new CriteriaBuilder($this->em);
        $this->incoming['contactdesc'] = 1;
        
        $contactdesc = new Contactdesc();
        
        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repo);
        $this->repo->expects($this->any())
            ->method('find')
            ->willReturn($contactdesc);
        
        $criteria = $builder->getGeneralCriteria($this->incoming);

        $this->assertEquals('1 = 1', $criteria['siteWhereClause']);
        $this->assertEquals([], $criteria['siteParameters']);
        $this->assertEquals('c.contactdesc = :contactdesc', $criteria['contactWhereClause']);
        $this->assertEquals(['contactdesc' => $contactdesc], $criteria['contactParameters']);
    }

    public function testGeneralCriteriaSiteNoCountyContactDesc() {
        $builder = new CriteriaBuilder($this->em);
        $this->incoming['center'] = 1;
        $this->incoming['contactdesc'] = 1;
        
        $center = new Center();
        $contactdesc = new Contactdesc();
        
        $this->em->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repo);
        $this->repo->expects($this->exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls($center, $contactdesc);
        
        $criteria = $builder->getGeneralCriteria($this->incoming);

        $this->assertEquals('c.center = :center', $criteria['siteWhereClause']);
        $this->assertEquals(['center' => $center], $criteria['siteParameters']);
        $this->assertEquals('c.contactdesc = :contactdesc', $criteria['contactWhereClause']);
        $this->assertEquals(['contactdesc' => $contactdesc], $criteria['contactParameters']);
    }

}
