<?php

namespace CreativeSoftworks\BehatWiremockContextExtension\Mappings;

use Guzzle\Http\Client;

class MappingsService
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
     * @param \Guzzle\Http\Client $client
     * @param string $wiremockBaseUrl
     */
    public function __construct(Client $client, $wiremockBaseUrl) {
        $this->client = $client;
        $this->wiremockBaseUrl = $wiremockBaseUrl;
    }
    
    /**
     * @throws \RuntimeException
     */
    public function resetMappings()
    {
        $response = $this->client->post($this->wiremockBaseUrl . self::WIREMOCK_RESET_PATH)->send();
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Resetting wiremock stubs failed. Called url was ' . $this->wiremockBaseUrl . self::WIREMOCK_RESET_PATH);
        }
    }
    
    /**
     * @param string $mappingPath
     */
    public function loadMapping($mappingPath)
    {
        $this->postWiremockMapping($this->readMappingFile($mappingPath));
    }
    
    /**
     * @param string $mappingFilePath
     * @return string
     * @throws \InvalidArgumentException
     */
    private function readMappingFile($mappingFilePath)
    {
        $mapping = file_get_contents($mappingFilePath);
        if (false === $mapping) {
            throw new \InvalidArgumentException('Mapping ' . $mappingFilePath . ' could not be read.');
        }
        return $mapping;
    }
    
    /**
     * @param string $mapping
     * @throws \RuntimeException
     */
    private function postWiremockMapping($mapping)
    {
        $response = $this->client->post($this->wiremockBaseUrl . self::WIREMOCK_NEW_MAPPING_PATH, null, $mapping)->send();
        if ($response->getStatusCode() !== 201) {
            throw new \RuntimeException(sprintf('Failed to create stub. Called Url was %s%s and mapping used was %s', $this->wiremockBaseUrl, self::WIREMOCK_NEW_MAPPING_PATH, $mapping));
        }        
    }
}
