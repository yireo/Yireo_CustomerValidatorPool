<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Test\Unit\Validator;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Validator\Exception;
use PHPUnit\Framework\TestCase;
use Yireo\CustomerValidatorPool\Validator\CustomerFirstNameLengthValidator;

class CustomerFirstNameLengthValidatorTest extends TestCase
{
    public function testValidNameOfExactMinimumLengthPasses(): void
    {
        $validator = new CustomerFirstNameLengthValidator(4);
        $this->assertTrue($validator->validate($this->getCustomer('Jane')));
    }

    public function testTooShortNameThrowsException(): void
    {
        $validator = new CustomerFirstNameLengthValidator(4);
        $this->expectException(Exception::class);
        $validator->validate($this->getCustomer('Jan'));
    }

    public function testMultibyteCharactersAreCountedAsCharacters(): void
    {
        $validator = new CustomerFirstNameLengthValidator(3);
        $this->expectException(Exception::class);
        $validator->validate($this->getCustomer('Ål'));
    }

    public function testNullFirstNameThrowsValidatorException(): void
    {
        $validator = new CustomerFirstNameLengthValidator(1);
        $this->expectException(Exception::class);
        $validator->validate($this->getCustomer(null));
    }

    public function testNullFirstNamePassesWithoutMinimumLength(): void
    {
        $validator = new CustomerFirstNameLengthValidator(0);
        $this->assertTrue($validator->validate($this->getCustomer(null)));
    }

    public function testMessageDoesNotContainUserInput(): void
    {
        $firstName = '<svg onload=alert(1)>';
        $validator = new CustomerFirstNameLengthValidator(30);

        try {
            $validator->validate($this->getCustomer($firstName));
            $this->fail('Expected exception was not thrown');
        } catch (Exception $exception) {
            $this->assertStringNotContainsString($firstName, $exception->getMessage());
            $this->assertStringContainsString('30', $exception->getMessage());
        }
    }

    private function getCustomer(?string $firstName): CustomerInterface
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getFirstname')->willReturn($firstName);

        return $customer;
    }
}
