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

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Services\GeneralStatisticsReport;

/**
 * Description of ResSizeTest
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class ResSizeTest extends TestCase
{

    // https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testSetResDist()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $stats = new GeneralStatisticsReport($this->em);

        $parametersEq = [$this->sizeA, $this->resA];
        $dataEq = $this->invokeMethod($stats, 'setResDist', $parametersEq);
        $this->assertEquals($this->finalResEqSize, $dataEq);
        
        $parametersLT = [$this->sizeA, $this->resB];
        $dataLT = $this->invokeMethod($stats, 'setResDist', $parametersLT);
        $this->assertEquals($this->finalResLTSize, $dataLT);
        
        $parametersGT = [$this->sizeB, $this->resA];
        $dataGT = $this->invokeMethod($stats, 'setResDist', $parametersGT);
        $this->assertEquals($this->finalResGTSize, $dataGT);
    }

    public function setup()
    {
        $this->sizeA = [
            ['id' => '1', 'size' => '1'],
            ['id' => '2', 'size' => '2'],
            ['id' => '3', 'size' => '3'],
        ];
        $this->sizeB = [
            ['id' => '1', 'size' => '1'],
            ['id' => '2', 'size' => '2'],
        ];
        $this->resA = [
            ['id' => '1', 'R' => '0'],
            ['id' => '2', 'R' => '20'],
            ['id' => '3', 'R' => '50'],
        ];
        $this->resB = [
            ['id' => '1', 'R' => '0'],
            ['id' => '3', 'R' => '30'],
        ];
        // resA & sizeB
        $this->finalResGTSize = [
            "< 1 month" => 1,
            "1 mo - 2 yrs" => 2,
            ">=2 yrs" => 0,
        ];
        // resB & sizeA
        $this->finalResLTSize = [
            "< 1 month" => 1,
            "1 mo - 2 yrs" => 0,
            ">=2 yrs" => 3,
       ];
        // resA & sizeA
        $this->finalResEqSize = [
            "< 1 month" => 1,
            "1 mo - 2 yrs" => 2,
            ">=2 yrs" => 3,
        ];

//        $this->assertNotEquals($sizeA, $resA);
//        $this->assertEquals(count($sizeA), count($resA));
    }
}
