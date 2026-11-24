<?php declare(strict_types=1);

namespace Yireo\CustomerValidatorPool\Test\Integration;

use Magento\Framework\Event\ConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Yireo\CustomerValidatorPool\Observer\AddValidatorPool;
use Yireo\CustomerValidatorPool\Validator\CustomerFirstNameLengthValidator;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool;
use Yireo\IntegrationTestHelper\Test\Integration\AbstractTestCase;

class ModuleTest extends AbstractTestCase
{
    public function testIfModuleIsWorking(): void
    {
        $this->assertModuleIsRegistered('Yireo_CustomerValidatorPool');
        $this->assertModuleIsEnabled('Yireo_CustomerValidatorPool');
    }

    public function testIfObserverIsRegistered(): void
    {
        $eventConfig = Bootstrap::getObjectManager()->get(ConfigInterface::class);
        $observers = $eventConfig->getObservers('customer_save_before');

        $this->assertArrayHasKey('customer_save_before_validator_pool', $observers);
        $this->assertSame(
            AddValidatorPool::class,
            ltrim($observers['customer_save_before_validator_pool']['instance'], '\\')
        );
    }

    public function testIfPoolContainsDefaultValidator(): void
    {
        $pool = Bootstrap::getObjectManager()->get(CustomerValidatorPool::class);
        $validators = $pool->getValidators();

        $this->assertArrayHasKey('first-name-length', $validators);
        $this->assertInstanceOf(CustomerFirstNameLengthValidator::class, $validators['first-name-length']);
    }
}
