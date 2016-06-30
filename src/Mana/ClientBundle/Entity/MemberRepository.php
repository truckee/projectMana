<?php

namespace Mana\ClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of MemberRepository.
 *
 * @author George
 */
class MemberRepository extends EntityRepository
{
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
