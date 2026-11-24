<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Test\Unit\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yireo\CustomerValidatorPool\Observer\AddValidatorPool;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;

class AddValidatorPoolTest extends TestCase
{
    public function testAllValidatorsAreCalled(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $validator1 = $this->createMock(CustomerValidatorInterface::class);
        $validator1->expects($this->once())->method('validate')->with($customer)->willReturn(true);
        $validator2 = $this->createMock(CustomerValidatorInterface::class);
        $validator2->expects($this->once())->method('validate')->with($customer)->willReturn(true);

        $observer = new AddValidatorPool(new CustomerValidatorPool([$validator1, $validator2]));
        $observer->execute($this->getObserver($this->getCustomerModel($customer, false)));
    }

    public function testValidatorsAreSkippedWhenIgnoreValidationFlagIsSet(): void
    {
        $validator = $this->createMock(CustomerValidatorInterface::class);
        $validator->expects($this->never())->method('validate');

        $customerModel = $this->getCustomerModel($this->createMock(CustomerInterface::class), true);
        $customerModel->expects($this->never())->method('getDataModel');

        $observer = new AddValidatorPool(new CustomerValidatorPool([$validator]));
        $observer->execute($this->getObserver($customerModel));
    }

    public function testValidatorsAreSkippedWithoutCustomer(): void
    {
        $validator = $this->createMock(CustomerValidatorInterface::class);
        $validator->expects($this->never())->method('validate');

        $observer = new AddValidatorPool(new CustomerValidatorPool([$validator]));
        $observer->execute($this->getObserver(null));
        $observer->execute($this->getObserver(new DataObject()));
    }

    private function getCustomerModel(CustomerInterface $customer, bool $ignoreValidation): Customer&MockObject
    {
        $customerModel = $this->createMock(Customer::class);
        $customerModel->method('getData')
            ->willReturnCallback(fn ($key = '') => $key === 'ignore_validation_flag' ? $ignoreValidation : null);
        $customerModel->method('getDataModel')->willReturn($customer);

        return $customerModel;
    }

    private function getObserver(?object $customerModel): Observer
    {
        $event = new Event(['customer' => $customerModel]);

        return new Observer(['event' => $event]);
    }
}
