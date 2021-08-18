<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Validator;

class CustomerValidatorPool
{
    /**
     * @var CustomerValidatorInterface[]
     */
    private $customerValidators;

    /**
     * @param CustomerValidatorInterface[] $customerValidators
     */
    public function __construct(
        array $customerValidators = []
    ) {
        $this->customerValidators = $customerValidators;
    }

    /**
     * @return CustomerValidatorInterface[]
     */
    public function getValidators(): array
    {
        return $this->customerValidators;
    }
}
