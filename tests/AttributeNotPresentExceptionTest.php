<?php
namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Internal\Exceptions\AttributeNotPresentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AttributeNotPresentException::class)]
class AttributeNotPresentExceptionTest extends TestCase {
    public function testToManyAttributesPresentExceptionConvertsToStringCorrectly() :void {
        $e = new AttributeNotPresentException("test");
        $this->assertEquals("Attribute test not present.", "{$e}");
    }
}
