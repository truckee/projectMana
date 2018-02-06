<?php
/*
 * This file is part of the Truckee\Projectmana package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\Utilities\PdfService.php

namespace Truckee\ProjectmanaBundle\Utilities;

/**
 * PdfService
 *
 */
class PdfService
{
    private $os;

    public function __construct($os)
    {
        $this->os = $os;
    }

    public function pdfExecutable()
    {
        return $this->os;
    }
}
