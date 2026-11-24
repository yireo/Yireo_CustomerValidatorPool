<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Validator;

use InvalidArgumentException;

class CustomerValidatorPool
{
    /**
     * @var CustomerValidatorInterface[]
     */
    private array $customerValidators = [];

    /**
     * @param array<string|int, mixed> $customerValidators
     * @throws InvalidArgumentException
     */
    public function __construct(
        array $customerValidators = []
    ) {
        foreach ($customerValidators as $name => $customerValidator) {
            if (!$customerValidator instanceof CustomerValidatorInterface) {
                throw new InvalidArgumentException(
                    'Customer validator "' . $name . '" does not implement ' . CustomerValidatorInterface::class
                );
            }

            $this->customerValidators[$name] = $customerValidator;
        }
    }

    /**
     * @return CustomerValidatorInterface[]
     */
    public function getValidators(): array
    {
        return $this->customerValidators;
    }
}
