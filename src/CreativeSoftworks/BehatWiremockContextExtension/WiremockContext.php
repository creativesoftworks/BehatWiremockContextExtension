<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

use Guzzle\Http\Client;
use Behat\Gherkin\Node\TableNode;

class WiremockContext
{
    const WIREMOCK_RESET_PATH = '/__admin/mappings/reset';
    const WIREMOCK_NEW_MAPPING_PATH = '/__admin/mappings/new';
    
    /**
     * @var \Guzzle\Http\Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $wiremockBaseUrl;

    /**
     * @var string
     */
    private $wiremockMappingsPath;
    
    /**
     * @param \Guzzle\Http\Client $client
     * @param string $wiremockBaseUrl
     * @param string $wiremockMappingsPath
     */
    public function __construct(Client $client, $wiremockBaseUrl, $wiremockMappingsPath) {
        $this->client = $client;
        $this->wiremockBaseUrl = $wiremockBaseUrl;
        $this->wiremockMappingsPath = $wiremockMappingsPath;
    }
   
    /**
     * @BeforeScenario
     */
    public function resetWiremockMappings()
    {
        $response = $this->client->post($this->wiremockBaseUrl . self::WIREMOCK_RESET_PATH)->send();
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Resetting wiremock stubs failed. Called url was ' . $this->wiremockBaseUrl . self::WIREMOCK_RESET_PATH);
        }
    }
    
    /**
     * @Given /^the following services exist:$/
     */
    public function theFollowingServicesExist(TableNode $table)
    {
        $mappingsDirectory = $this->wiremockMappingsPath;
        $stubs = $table->getHash();
        foreach ($stubs as $stub) {
            if (!array_key_exists('service', $stub) || !array_key_exists('mapping', $stub)) {
                throw new \InvalidArgumentException('Table must contain the keys "service" and "mapping"');
            }
            $mappingFilePath = $mappingsDirectory . '/' . $stub['service'] . '/' . $stub['mapping'];
            $mapping = file_get_contents($mappingFilePath);

            if (false === $mapping) {
                throw new \InvalidArgumentException('Mapping ' . $mappingFilePath . ' could not be read.');
            }
            
            $response = $this->client->post($this->wiremockBaseUrl . self::WIREMOCK_NEW_MAPPING_PATH, null, $mapping)->send();
            if ($response->getStatusCode() !== 201) {
                throw new \RuntimeException(sprintf('Failed to create stub. Called Url was %s%s and mapping used was %s', $this->wiremockBaseUrl, self::WIREMOCK_NEW_MAPPING_PATH, $mapping));
            }
        }
    }
}
