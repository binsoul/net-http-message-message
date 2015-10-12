<?php

namespace BinSoul\Test\Net\Http\Message\Part;

use BinSoul\Net\Http\Message\Part\Status;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function validStatus()
    {
        return [
            [100, 'Continue'],
            [599, ''],
            [200, 'OK'],
            [304, 'Not Modified'],
            [410, 'Gone'],
        ];
    }

    /**
     * @dataProvider validStatus
     */
    public function test_constructor_converts_code_to_integer($testCode)
    {
        $this->assertSame($testCode, (new Status((string) $testCode))->getCode());
    }

    /**
     * @dataProvider validStatus
     */
    public function test_detects_valid_codes($testCode)
    {
        $this->assertTrue(Status::isValid($testCode));
    }

    /**
     * @dataProvider validStatus
     */
    public function test_returns_phrase($testCode, $expectedPhrase)
    {
        $this->assertEquals($expectedPhrase, (new Status($testCode))->getPhrase());
    }

    public function invalidStatus()
    {
        return [
            [99],
            [600],
            ['abc'],
            [null],
            [[]],
        ];
    }

    /**
     * @dataProvider invalidStatus
     * @expectedException \InvalidArgumentException
     */
    public function test_constructor_raises_exception_for_invalid_code($testCode)
    {
        new Status($testCode);
    }

    /**
     * @dataProvider invalidStatus
     */
    public function test_detects_invalid_codes($testCode)
    {
        $this->assertFalse(Status::isValid($testCode));
    }
}
