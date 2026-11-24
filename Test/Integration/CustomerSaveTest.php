<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Test\Integration;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yireo\CustomerValidatorPool\Observer\AddValidatorPool;
use Yireo\CustomerValidatorPool\Validator\CustomerFirstNameLengthValidator;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;

/**
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 */
class CustomerSaveTest extends TestCase
{
    private const PASSWORD = 'Password123!';

    private ObjectManager $objectManager;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->assertInstanceOf(ObjectManager::class, $objectManager);
        $this->objectManager = $objectManager;
    }

    protected function tearDown(): void
    {
        $this->setMinimumLength(1);
    }

    public function testCreateAccountFailsWithTooShortFirstName(): void
    {
        $this->setMinimumLength(5);

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('First name should be 5 or more characters');
        $this->getAccountManagement()->createAccount($this->getNewCustomer('Jan'), self::PASSWORD);
    }

    public function testCreateAccountSucceedsWithValidFirstName(): void
    {
        $this->setMinimumLength(5);

        $customer = $this->getAccountManagement()->createAccount($this->getNewCustomer('Janine'), self::PASSWORD);
        $this->assertNotEmpty($customer->getId());
    }

    public function testPasswordResetWorksForCustomerWithInvalidFirstName(): void
    {
        $customer = $this->getAccountManagement()->createAccount($this->getNewCustomer('Jan'), self::PASSWORD);
        $this->setMinimumLength(5);

        $accountManagement = $this->get(AccountManagement::class);
        $token = 'abcdefghijklmnopqrstuvwxyz012345';
        $customer = $this->get(CustomerRepositoryInterface::class)->getById((int)$customer->getId());
        $this->assertTrue($accountManagement->changeResetPasswordLinkToken($customer, $token));
        $this->assertTrue($accountManagement->resetPassword(
            (string)$customer->getEmail(),
            $token,
            'NewPassword456!'
        ));
    }

    public function testRegularSaveFailsForCustomerWithInvalidFirstName(): void
    {
        $customer = $this->getAccountManagement()->createAccount($this->getNewCustomer('Jan'), self::PASSWORD);
        $this->setMinimumLength(5);

        $customerRepository = $this->get(CustomerRepositoryInterface::class);
        $customer = $customerRepository->getById((int)$customer->getId());
        $customer->setLastname('Changed');

        $this->expectException(ValidatorException::class);
        $customerRepository->save($customer);
    }

    private function setMinimumLength(int $minimumLength): void
    {
        $pool = new CustomerValidatorPool([
            'first-name-length' => new CustomerFirstNameLengthValidator($minimumLength),
        ]);

        $this->objectManager->addSharedInstance($pool, CustomerValidatorPool::class);
        $this->objectManager->addSharedInstance(new AddValidatorPool($pool), AddValidatorPool::class);
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T
     */
    private function get(string $className): object
    {
        $object = $this->objectManager->get($className);
        if (!$object instanceof $className) {
            throw new RuntimeException('Unable to instantiate ' . $className);
        }

        return $object;
    }

    private function getAccountManagement(): AccountManagementInterface
    {
        return $this->get(AccountManagementInterface::class);
    }

    private function getNewCustomer(string $firstName): CustomerInterface
    {
        $customer = $this->get(CustomerInterfaceFactory::class)->create();
        $customer->setFirstname($firstName);
        $customer->setLastname('Doe');
        $customer->setEmail('validator-pool-' . uniqid() . '@example.com');
        $customer->setWebsiteId(1);
        $customer->setStoreId(1);
        $customer->setGroupId(1);

        return $customer;
    }
}
