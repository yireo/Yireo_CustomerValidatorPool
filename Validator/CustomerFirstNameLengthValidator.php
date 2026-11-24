<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Validator;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Validator\Exception;

class CustomerFirstNameLengthValidator implements CustomerValidatorInterface
{
    /**
     * @param int $minimumLength
     */
    public function __construct(
        private readonly int $minimumLength = 0
    ) {
    }

    /**
     * @param CustomerInterface $customer
     * @return bool
     * @throws Exception
     */
    public function validate(CustomerInterface $customer): bool
    {
        if (mb_strlen((string)$customer->getFirstname()) < $this->minimumLength) {
            throw new Exception(
                __('First name should be %1 or more characters', $this->minimumLength)
            );
        }

        return true;
    }
}
