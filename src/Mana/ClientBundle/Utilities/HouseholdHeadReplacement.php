<?php

//src\Mana\ClientBundle\Utilities\HouseholdHeadReplacement.php

namespace Mana\ClientBundle\Utilities;

/**
 * Description of HouseholdHeadReplacement
 *
 * @author George
 */
class HouseholdHeadReplacement
{

    public function replaceHeadData($data)
    {
        $formerHeadId = $data['headId'];  //original hoh_id
        $newHeadId = $data['isHead'];  //new hoh_id
        if ($newHeadId <> $formerHeadId) {
            //get the updated values
            $v1 = false;
            foreach ($data['members'] as $member) {
                if (array_key_exists('id', $member) && $member['id'] == $newHeadId) {
                    $dob = $member['dob'];
                    $sex = $member['sex'];
                    $ethnicity = $member['ethnicity'];
                }
                elseif ($member['id'] == $formerHeadId && !array_key_exists('dob', $member)) {
                    $v1 = true;
                }
            }
            for ($index = 0; $index < count($data['members']); $index++) {
                //set the new values
                if (array_key_exists('id', $data['members'][$index]) && $data['members'][$index]['id'] == $formerHeadId && $v1 == true) {
                    $data['members'][$index]['dob'] = $dob;
                    $data['members'][$index]['sex'] = $sex;
                    $data['members'][$index]['ethnicity'] = $ethnicity;
                }
            }
        }
        
        return $data;
    }

}
