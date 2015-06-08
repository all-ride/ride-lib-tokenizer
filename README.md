# Ride: Tokenizer Library

This library gives you some classes to parse a string into tokens.

## Code Sample

Some example code in the context of the ORM module:

```php    
<?php

use ride\library\tokenizer\symbol\NestedSymbol;
use ride\library\tokenizer\symbol\SimpleSymbol;
use ride\library\tokenizer\Tokenizer;

$tokenizer = new Tokenizer();
$tokenizer->setWillTrimTokens(true);
$tokenizer->addSymbol(new SimpleSymbol('AND'));
$tokenizer->addSymbol(new SimpleSymbol('OR'));
$tokenizer->addSymbol(new NestedSymbol('(', ')', $tokenizer));

$condition = '{field} = %2% AND {field2} <= %1%';
$tokens = $tokenizer->tokenize($condition);
// array(
//    '{field} = %2%', 
//    'AND', 
//    '{field2} <= %1%'
// )

$condition = '{field} = 5 AND ({field2} <= %1% OR {field2} >= %2%)';
$tokens = $tokenizer->tokenize($condition);
// array(
//    '{field} = 5', 
//    'AND', 
//    array(
//        '{field2} <= %1%'), 
//        'OR', 
//        '{field2} >= %2%'),
//    )
// )
```
