# Customer validator pool
While Magento makes it easy (?) to validate customer fields in the frontend (for instance, by using the XML layout or JavaScript validators), on the PHP-side, there is no architecture for this. Fair enough, you can just use events or DI plugins for this. However, this module introduces the concept of a pool of validator classes, which can easily be added via XML.

For this to work, you would create a new class that implements `Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface`:
```xml
namespace JisseReitsma\OnlyMeGetsCake\Validator;

use Yireo\CustomerValidatorPool\Validator\CustomerValidatorInterface;
use Magento\Framework\Validator\Exception;

class CakeValidator implements CustomerValidatorInterface
{
    /**
     * @param CustomerInterface $customer
     * @return bool
     * @throws Exception
     */
    public function validate(CustomerInterface $customer): bool
    {
        $dob = date('Y-m-d', strtotime($customer->getDob()));
        if ($dob === '1978-07-02') {
            throw new Exception(
                __('On this date, nobody else gets cake but Jisse Reitsma')
            );
        }
    }
}
```

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

### Todo
- Create configuration values in Store Configuration to easily adjust parameters
