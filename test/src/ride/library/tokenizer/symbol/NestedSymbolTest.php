<?php

namespace ride\library\tokenizer\symbol;

use PHPUnit\Framework\TestCase;

class NestedSymbolTest extends TestCase {

    /**
     * @dataProvider provideTokenize
     */
    public function testTokenize($expected, $expectedProcess, $process, $toProcess, $allowsSymbolsBeforeOpen = true, $open = '(', $close = ')') {
        $symbol = new NestedSymbol($open, $close, null, false, $allowsSymbolsBeforeOpen);

        $result = $symbol->tokenize($process, $toProcess);

        $this->assertEquals($expected, $result);
        $this->assertEquals($expectedProcess, $process);
    }

    /**
     * @expectedException ride\library\tokenizer\exception\TokenizeException
     * @expectedExceptionMessage Provided open symbol is empty or not a string
     */
    public function testConstructorOnEmptyOpenSymbol() {
        $symbol = new NestedSymbol('', ']', null, true, true);
    }

    /**
     * @expectedException ride\library\tokenizer\exception\TokenizeException
     * @expectedExceptionMessage Provided open symbol is empty or not a string
     */
    public function testConstructorOnEmptycCloseSymbol() {
        $symbol = new NestedSymbol('[', '', null, true, true);
    }

    public function testAllowsSymbolsBeforeOpen() {
        $symbol = new NestedSymbol('[', ']', null, true, true);

        $this->assertTrue($symbol->allowsSymbolsBeforeOpen());
    }

    /**
     * @dataProvider provideTokenizeWithIncludeSymbols
     */
    public function testTokenizeWithIncludeSymbols($expected, $expectedProcess, $process, $toProcess, $allowsSymbolsBeforeOpen = true, $open = '(', $close = ')') {
        $symbol = new NestedSymbol($open, $close, null, true, $allowsSymbolsBeforeOpen);

        $result = $symbol->tokenize($process, $toProcess);

        $this->assertEquals($expected, $result);
        $this->assertEquals($expectedProcess, $process);
    }

    public function provideTokenizeWithIncludeSymbols() {
        return array(
            array(null, 'test', 'test', 'test and test'),
            array(array('yes ', '(', 'test and test', ')'), 'yes (test and test)', 'yes (', 'yes (test and test)'),
            array(array('yes ', '(', 'test (and test)', ')'), 'yes (test (and test))', 'yes (', 'yes (test (and test))'),
            array(null, 'yes (', 'yes (', 'yes (test (and test))', false),
            array(array('yes ', '"', 'test and test', '"'), 'yes "test and test"', 'yes "', 'yes "test and test" and "test"', true, '"', '"'),
        );
    }

    public function provideTokenize() {
        return array(
            array(null, 'test', 'test', 'test and test'),
            array(array('yes ', 'test and test'), 'yes (test and test)', 'yes (', 'yes (test and test)'),
            array(array('yes ', 'test (and test)'), 'yes (test (and test))', 'yes (', 'yes (test (and test))'),
            array(null, 'yes (', 'yes (', 'yes (test (and test))', false),
            array(array('yes ', 'test and test'), 'yes "test and test"', 'yes "', 'yes "test and test" and "test"', true, '"', '"'),
        );
    }

}
