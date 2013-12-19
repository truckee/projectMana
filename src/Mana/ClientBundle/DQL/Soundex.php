<?php

namespace Mana\ClientBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * SoundexFunction ::= "Soundex" "(" StringPrimary  ")"
 */
class Soundex extends FunctionNode
{
    public $stringExpression;
    
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->stringExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
        
    }
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'Soundex(' .
            $this->stringExpression->dispatch($sqlWalker) .
        ')';
    }
}
?>
