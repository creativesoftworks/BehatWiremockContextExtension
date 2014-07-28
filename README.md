BehatWiremockContextExtension
===============
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/creativesoftworks/BehatWiremockContextExtension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/creativesoftworks/BehatWiremockContextExtension/?branch=master)[![Build Status](https://travis-ci.org/creativesoftworks/BehatWiremockContextExtension.svg?branch=master)](https://travis-ci.org/creativesoftworks/BehatWiremockContextExtension)

A Behat Extension that makes use of Wiremock as a test double for API calls.

## Versions and compatibility
* Versions 1.x are compatible with behat 2.4+
* No compatibility for behat 3 (yet...)
* No compatibility for Windows

## Installation
Via composer:
```json
"require-dev": {
    "creativesoftworks/behat-wiremock-context-extension": "1.*"
}
```

## Configuration
Configuration options, in your behat.yml:
```yaml
your_profile_name:
    extensions:
        CreativeSoftworks\BehatWiremockContextExtension\Extension:
            wiremock_base_url: 'http://localhost:8080'
            wiremock_mappings_path: 'app/Resources/wiremock/mappings'
```

The extension will reset the wiremock mappings before each scenario execution, so all mappings under wiremock_mappings_path will get loaded with no guaranteed order.
Frequently you would want to guarantee that certain mappings will become defaults in wiremock (usually successful responses instead of failed ones).
In order for that to happen you can define an array of mapping filepaths, relative to wiremock_mappings_path, like in this example:

```yaml
            default_mappings:
                - "service-name/mapping-file.json"
```

## Usage
Your FeatureContext class must implement the [CreativeSoftworks\BehatWiremockContextExtension\WiremockContextAware](https://github.com/creativesoftworks/BehatWiremockContextExtension/blob/master/src/CreativeSoftworks/BehatWiremockContextExtension/WiremockContextAware.php) interface, here's an example:

```php
class FeatureContext implements WiremockContextAware
{
    /**
     * @return \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext
     */
    public function getWiremockContext()
    {
        return $this->getSubcontext('WiremockContext');
    }

    /**
     * @param \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext $wiremockContext
     */
    public function setWiremockContext(WiremockContext $wiremockContext)
    {
        $this->useContext('WiremockContext', $wiremockContext);
    }
}
```