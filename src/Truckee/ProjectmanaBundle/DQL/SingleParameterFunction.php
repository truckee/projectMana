<?php
/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\ProjectmanaBundle\DQL\SingleParameterFunction.php

namespace Truckee\ProjectmanaBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * SingleParameterFunction
 *
 */
class SingleParameterFunction extends FunctionNode
{
    public $expression = null;
    protected $functionName = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return $this->functionName . '(' . $this->expression->dispatch($sqlWalker) . ')';
    }
}
