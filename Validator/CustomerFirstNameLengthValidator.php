<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Validator;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Validator\Exception;

class CustomerFirstNameLengthValidator implements CustomerValidatorInterface
{
    /**
     * @var int
     */
    private $minimumLength;

    /**
     * @param int $minimumLength
     */
    public function __construct(
        int $minimumLength = 0
    ) {
        $this->minimumLength = $minimumLength;
    }

    /**
     * @param CustomerInterface $customer
     * @return bool
     * @throws Exception
     */
    public function validate(CustomerInterface $customer): bool
    {
        if (strlen($customer->getFirstname()) < $this->minimumLength) {
            throw new Exception(
                __('First name "%1" should be %2 or more characters',
                    $customer->getFirstname(),
                    $this->minimumLength)
            );
        }

        return true;
    }
}
