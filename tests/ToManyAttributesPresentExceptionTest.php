<?php
namespace Gregs\AttributeRouter\Tests;

use Gregs\AttributeRouter\Exceptions\ToManyAttributesPresentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ToManyAttributesPresentException::class)]
class ToManyAttributesPresentExceptionTest extends TestCase {
    public function testToManyAttributesPresentExceptionConvertsToStringCorrectly() :void {
        $e = new ToManyAttributesPresentException("a","b");
        $this->assertEquals("Multiple a attributes present on b", "{$e}");
    }
}
