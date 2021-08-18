<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Validator;

use Magento\Customer\Api\Data\CustomerInterface;

interface CustomerValidatorInterface
{
    /**
     * @param CustomerInterface $customer
     * @return bool
     */
    public function validate(CustomerInterface $customer): bool;
}
