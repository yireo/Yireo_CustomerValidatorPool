<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Test\Unit\Validator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;

class CustomerValidatorPoolTest extends TestCase
{
    public function testValidValidatorsAreReturned(): void
    {
        $validator = $this->createMock(CustomerValidatorInterface::class);
        $pool = new CustomerValidatorPool(['foo' => $validator]);

        $this->assertSame(['foo' => $validator], $pool->getValidators());
    }

    public function testEmptyPool(): void
    {
        $this->assertSame([], (new CustomerValidatorPool())->getValidators());
    }

    public function testInvalidValidatorThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"bar"');
        new CustomerValidatorPool(['bar' => new stdClass()]);
    }
}
