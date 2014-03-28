<?php

namespace ride\library\tokenizer\symbol;

use ride\library\tokenizer\exception\TokenizeException;
use ride\library\tokenizer\Tokenizer;

/**
 * Nested symbol for the tokenizer
 */
class NestedSymbol extends AbstractSymbol {

    /**
     * Tokenizer to tokenize the value between the open and close symbol
     * @var \ride\library\tokenizer\Tokenizer
     */
    protected $tokenizer;

    /**
     * Open symbol of the token
     * @var string
     */
    protected $symbolOpen;

    /**
     * Length of the open symbol
     * @var integer
     */
    protected $symbolOpenLength;

    /**
     * Length of the open symbol multiplied with -1
     * @var integer
     */
    protected $symbolOpenOffset;

    /**
     * Close symbol of the token
     * @var string
     */
    protected $symbolClose;

    /**
     * Length of the close symbol
     * @var integer
     */
    protected $symbolCloseLength;

    /**
     * Flag to set whether to allow symbols before the open symbol
     * @var boolean
     */
    protected $allowsSymbolsBeforeOpen;

    /**
     * Constructs a new nested tokenizer
     * @param string $symbolOpen Open symbol of the token
     * @param string $symbolClose Close symbol of the token
     * @param \ride\library\tokenizer\Tokenizer $tokenizer When provided, the
     * value between the open and close symbol will be tokenized using this
     * tokenizer
     * @param boolean $willIncludeSymbols True to include the open and close
     * symbol in the tokenize result, false otherwise
     * @return null
     */
    public function __construct($symbolOpen, $symbolClose, Tokenizer $tokenizer = null, $willIncludeSymbols = false, $allowsSymbolsBeforeOpen = true) {
        $this->setOpenSymbol($symbolOpen);
        $this->setCloseSymbol($symbolClose);
        $this->setWillIncludeSymbols($willIncludeSymbols);
        $this->setAllowsSymbolsBeforeOpen($allowsSymbolsBeforeOpen);

        $this->tokenizer = $tokenizer;
    }

    /**
     * Checks for this symbol in the string which is being tokenized
     * @param string $process Current part of the string which is being
     * tokenized
     * @param string $toProcess Remaining part of the string which has not yet
     * been tokenized
     * @return null|array Null when the symbol was not found, an array with the
     * processed tokens if the symbol was found.
     */
    public function tokenize(&$process, $toProcess) {
        $processLength = strlen($process);
        if ($processLength < $this->symbolOpenLength || substr($process, $this->symbolOpenOffset) != $this->symbolOpen) {
            return null;
        }

        $positionOpen = $processLength - $this->symbolOpenLength;
        $positionClose = $this->getClosePosition($toProcess, $positionOpen);
        $lengthProcess = strlen($process) + $positionOpen;

        $before = substr($process, 0, $positionOpen);
        if (!$this->allowsSymbolsBeforeOpen && trim($before)) {
            return null;
        }

        $between = substr($toProcess, $positionOpen + $this->symbolOpenLength, $positionOpen + $positionClose - $lengthProcess);

        $process .= $between . $this->symbolClose;

        if ($this->tokenizer !== null) {
            $between = $this->tokenizer->tokenize($between);
        }

        if ($this->willIncludeSymbols) {
            return array($before, $this->symbolOpen, $between, $this->symbolClose);
        }

        return array($before, $between);
    }

    /**
     * Gets the position of the close symbol in a string
     * @param string $string String to look in
     * @param integer $initialOpenPosition The position of the open symbol for
     * which to find the close symbol
     * @return integer The position of the close symbol
     * @throws \ride\library\tokenizer\exception\TokenizeException when the symbol is opened but not closed
     */
    protected function getClosePosition($string, $initialOpenPosition) {
        $initialOpenPosition++;

        $closePosition = strpos($string, $this->symbolClose, $initialOpenPosition);
        if ($closePosition === false) {
            throw new TokenizeException($this->symbolOpen . ' opened (at ' . $initialOpenPosition . ') but not closed for ' . $string);
        }

        $openPosition = strpos($string, $this->symbolOpen, $initialOpenPosition);
        if ($openPosition === false || $openPosition > $closePosition || $this->symbolClose == $this->symbolOpen) {
            return $closePosition;
        }

        $openClosePosition = $this->getClosePosition($string, $openPosition);

        return $this->getClosePosition($string, $openClosePosition);
    }

    /**
     * Sets the open symbol
     * @param string $symbol
     * @return null
     * @throws \ride\library\tokenizer\exception\TokenizerException when the provided symbol is empty or not a
     * string
     */
    private function setOpenSymbol($symbol) {
        if (!is_string($symbol) || $symbol == '') {
            throw new TokenizeException('Provided open symbol is empty or not a string');
        }

        $this->symbolOpen = $symbol;
        $this->symbolOpenLength = strlen($symbol);
        $this->symbolOpenOffset = $this->symbolOpenLength * -1;
    }

    /**
     * Sets the close symbol
     * @param string $symbol
     * @return null
     * @throws \ride\library\tokenizer\exception\TokenizerException when the provided symbol is empty or not a
     * string
     */
    private function setCloseSymbol($symbol) {
        if (!is_string($symbol) || $symbol == '') {
            throw new TokenizeException('Provided close symbol is empty or not a string');
        }

        $this->symbolClose = $symbol;
        $this->symbolCloseLength = strlen($symbol);
    }

    /**
     * Sets whether to allow symbols before the open symbol
     * @param boolean $flag
     * @return null
     */
    public function setAllowsSymbolsBeforeOpen($flag) {
        $this->allowsSymbolsBeforeOpen = $flag;
    }

    /**
     * Gets whether to allow symbols before the open symbol
     * @return boolean
     */
    public function allowsSymbolsBeforeOpen() {
        return $this->allowsSymbolsBeforeOpen;
    }

}