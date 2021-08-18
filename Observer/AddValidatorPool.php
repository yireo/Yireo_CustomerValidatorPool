<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;

class AddValidatorPool implements ObserverInterface
{
    /**
     * @var CustomerValidatorPool
     */
    private $customerValidatorPool;

    /**
     * @param CustomerValidatorPool $customerValidatorPool
     */
    public function __construct(
        CustomerValidatorPool $customerValidatorPool
    ) {
        $this->customerValidatorPool = $customerValidatorPool;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customerModel = $observer->getEvent()->getCustomer();
        if (!$customerModel) {
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
