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
        if (!$eventConfig instanceof ConfigInterface) {
            $this->fail('Unable to instantiate ' . ConfigInterface::class);
        }

        $observers = $eventConfig->getObservers('customer_save_before');

        $this->assertArrayHasKey('customer_save_before_validator_pool', $observers);
        $this->assertContains(
            $observers['customer_save_before_validator_pool']['instance'],
            [AddValidatorPool::class, '\\' . AddValidatorPool::class]
        );
    }

    public function testIfPoolContainsDefaultValidator(): void
    {
        $pool = Bootstrap::getObjectManager()->get(CustomerValidatorPool::class);
        if (!$pool instanceof CustomerValidatorPool) {
            $this->fail('Unable to instantiate ' . CustomerValidatorPool::class);
        }

        $validators = $pool->getValidators();

        $this->assertArrayHasKey('first-name-length', $validators);
        $this->assertInstanceOf(CustomerFirstNameLengthValidator::class, $validators['first-name-length']);
    }
}
