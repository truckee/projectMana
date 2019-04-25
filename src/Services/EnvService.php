<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\App\Services\EnvService.php

namespace App\Services;

use Symfony\Component\Dotenv\Dotenv;

class EnvService
{
    public function pdfExecutable()
    {
        $dotenv = new Dotenv();
        $dotenv->load('../.env');
        $exec = getenv('WKHTMLTOPDF_PATH');
        
        return $exec;
    }
}
