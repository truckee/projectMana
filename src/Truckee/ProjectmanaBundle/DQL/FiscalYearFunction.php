<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//src\Truckee\ProjectmanaBundle\DQL\FiscalYear.php

namespace Truckee\ProjectmanaBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * FiscalYearFunction ::= "FY" "(" ArithmeticPrimary ")"
 *
 */
class FiscalYearFunction extends FunctionNode
{
    public $dateExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'FY(' . $this->dateExpression->dispatch($sqlWalker) . ')';
    }
}
