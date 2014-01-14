<?php

namespace pallo\library\tokenizer\symbol;

/**
 * Abstract symbol for the tokenizer
 */
abstract class AbstractSymbol implements Symbol {

    /**
     * Flag to set whether to include defined symbols in the tokenize result
     * @var boolean
     */
    protected $willIncludeSymbols = false;

    /**
     * Sets whether to include defined symbols in the tokenize result
     * @param boolean $flag
     * @return null
     */
    public function setWillIncludeSymbols($flag) {
        $this->willIncludeSymbols = $flag;
    }

    /**
     * Gets whether to include defined symbols in the tokenize result
     * @return boolean
     */
    public function willIncludeSymbols() {
        return $this->willIncludeSymbols;
    }


}