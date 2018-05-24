<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\FYFunction.php

namespace Truckee\ProjectmanaBundle\Utilities;

/**
 * Description of FYFunction
 *
 * @author George
 */
trait FYFunction
{

    public function fy($date = null)
    {
        $year = date_format(new \DateTime($date), 'Y');
        $month = date_format(new \DateTime($date), 'n');
        $fy = ($month < 7) ? $year : $year + 1;

        return $fy;
    }
}
