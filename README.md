# Customer validator pool

<!-- badges.specs.start -->
<div class="github-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/customer-validator-pool)

</div>
<!-- badges.specs.end -->

While Magento makes it easy (?) to validate customer fields in the frontend (for instance, by using the XML layout or JavaScript validators), on the PHP-side, there is no architecture for this. Fair enough, you can just use events or DI plugins for this. However, this module introduces the concept of a pool of validator classes, which can easily be added via XML.

For this to work, you would create a new class that implements `Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface`:
```php
<?php declare(strict_types=1);

namespace JisseReitsma\OnlyMeGetsCake\Validator;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Validator\Exception;
use Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface;

class CakeValidator implements CustomerValidatorInterface
{
    /**
     * @param CustomerInterface $customer
     * @return bool
     * @throws Exception
     */
    public function validate(CustomerInterface $customer): bool
    {
        $dob = (string)$customer->getDob();
        if ($dob !== '' && date('Y-m-d', (int)strtotime($dob)) === '1978-07-02') {
            throw new Exception(
                __('On this date, nobody else gets cake but Jisse Reitsma')
            );
        }

        return true;
    }
}
```

Do not include the submitted value in the exception message: messages can be shown in the frontend without HTML escaping.

Next, the validator is registered via a DI type of the pool:
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Yireo\CustomerValidatorPool\Validator\CustomerValidatorPool">
        <arguments>
            <argument name="customerValidators" xsi:type="array">
                <item name="jisse-gets-all-the--cake" xsi:type="object">JisseReitsma\OnlyMeGetsCake\Validator\CakeValidator</item>
            </argument>
        </arguments>
    </type>
</config>
```

### Example: Minimum length for the first name
An example, shipped with this module, is the `CustomerFirstNameLengthValidator` class which checks for the minimum length of the first name. The default is set to `1`, but you can override this by adding a DI type:

File `etc/di.xml`:
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Yireo\CustomerValidatorPool\Validator\CustomerFirstNameLengthValidator">
        <arguments>
            <argument name="minimumLength" xsi:type="number">4</argument>
        </arguments>
    </type>
</config>
```

### Scope and limitations
The validators run on the `customer_save_before` event of the customer model in all areas (frontend, admin, REST, GraphQL, CLI). They are skipped when Magento sets the `ignore_validation_flag` (password reset, account confirmation, password hash upgrades), so existing customers with legacy data are not locked out.

This pool is meant for data quality, not as a security control: it is bypassed by the customer import, by `saveAttribute()` calls and by saves without data changes.

### Todo
- Create configuration values in Store Configuration to easily adjust parameters

## Current status

<!-- badges.test.start -->
<div class="github-pipeline-badges">

![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CustomerValidatorPool/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CustomerValidatorPool/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CustomerValidatorPool/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CustomerValidatorPool/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CustomerValidatorPool/compile.yml?label=compile)

</div>
<!-- badges.test.end -->
