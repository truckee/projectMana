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

use PHPUnit\Framework\TestCase;
use App\Services\Addresses;
use App\Entity\Address;
use App\Entity\AddressType;
use App\Entity\Household;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of AddressTemplatesTest
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class AddressTemplatesTest extends TestCase {
    public function setup() {
        $this->physical = 'Address/physicalAddressBlock.html.twig';
        $this->mailing = 'Address/mailingAddressBlock.html.twig';
        $this->existing = 'Address/existingAddressBlock.html.twig';
        $this->household = $this->createMock(Household::class);
        $this->address = $this->createMock(Address::class);
        $this->addressType = $this->createMock(AddressType::class);
    }
    
    public function testNoAddresses() {
        $this->household->method('getAddresses')->willReturn([]);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $addressTemplates = new Addresses($this->em);
        $templates = $addressTemplates->addressTemplates($this->household);
        
        $this->assertEquals([$this->physical, $this->mailing], $templates);
    }
    
    public function testPhysicalAddress() {
        $a = [$this->address];
        $this->household->method('getAddresses')->willReturn([$this->address]);
        $a[0]->method('getAddressType')->willReturn($this->addressType);
        $this->addressType->method('getAddressType')->willReturn('Physical');
        $this->em = $this->createMock(EntityManagerInterface::class);
        $addressTemplates = new Addresses($this->em);
        
        $templates = $addressTemplates->addressTemplates($this->household);
        
        $this->assertEquals([$this->mailing, $this->existing], $templates);
    }
    
    public function testMailingAddress() {
        $a = [$this->address];
        $this->household->method('getAddresses')->willReturn([$this->address]);
        $a[0]->method('getAddressType')->willReturn($this->addressType);
        $this->addressType->method('getAddressType')->willReturn('Mailing');
        $this->em = $this->createMock(EntityManagerInterface::class);
        $addressTemplates = new Addresses($this->em);
        
        $templates = $addressTemplates->addressTemplates($this->household);
        
        $this->assertEquals([$this->physical, $this->existing], $templates);
    }
    
    public function testBothTypes() {
        $this->household->method('getAddresses')->willReturn(['a', 'b']);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $addressTemplates = new Addresses($this->em);
        $templates = $addressTemplates->addressTemplates($this->household);
       
        $this->assertEquals([$this->existing], $templates);
    }
}
