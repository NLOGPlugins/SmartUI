<?php

namespace oat\beeme;

/**
 * Value object representing a function of mathematical expression.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class FunctionToken extends Operator
{
    /**
     * Create new FunctionToken object which represent one mathematical function.
     * 
     * @param string $value string representation of this function e.g. sin, cos, ...
     */
    public function __construct($value)
    {
        parent::__construct($value, 3, Operator::O_LEFT_ASSOCIATIVE);
        $this->type = Token::T_FUNCTION;
    }
}
