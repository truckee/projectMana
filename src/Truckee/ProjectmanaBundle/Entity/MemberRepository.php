<?php

/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Truckee\ProjectmanaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MemberRepository.
 *
 * @author George Brooks
 */
class MemberRepository extends EntityRepository
{
    /**
     * Default  member surname = household head's surname
     *
     * @param object $household
     */
    public function initialize($household)
    {
        $em = $this->getEntityManager();
        $members = $household->getMembers();
        $sname = $household->getHead()->getSname();
        // member default surname is head's surname
        foreach ($members as $member) {
            $memberSname = $member->getSname();
            if (empty($memberSname)) {
                $member->setSname($sname);
            }
            $em->persist($member);
        }
    }
}
