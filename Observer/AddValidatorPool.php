<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;

class AddValidatorPool implements ObserverInterface
{
    /**
     * @param CustomerValidatorPool $customerValidatorPool
     */
    public function __construct(
        private readonly CustomerValidatorPool $customerValidatorPool
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customerModel = $observer->getEvent()->getData('customer');
        if (!$customerModel instanceof Customer) {
            return;
        }

        if ($customerModel->getData('ignore_validation_flag')) {
            return;
        }

        $customer = $customerModel->getDataModel();
        if (!$customer instanceof CustomerInterface) {
            return;
        }

        foreach ($this->customerValidatorPool->getValidators() as $customerValidator) {
            $customerValidator->validate($customer);
        }
    }
}
