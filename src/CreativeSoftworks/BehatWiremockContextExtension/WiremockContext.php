<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

use CreativeSoftworks\BehatWiremockContextExtension\Mappings\MappingsService;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\BehatContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use CreativeSoftworks\BehatWiremockContextExtension\Event\MappingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WiremockContext extends BehatContext implements EventSubscriberInterface
{    
    /**
     * @var \CreativeSoftworks\BehatWiremockContextExtension\Mappings\MappingsService
     */
    private $mappingsService;
    
    /**
     * @var string
     */
    private $wiremockMappingsPath;
    
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $defaultMappingPaths;
    
    /**
     * @param \CreativeSoftworks\BehatWiremockContextExtension\Mappings\MappingsService $mappingsService
     * @param string $wiremockMappingsPath
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param array $defaultMappingPaths
     */
    public function __construct(MappingsService $mappingsService, $wiremockMappingsPath, EventDispatcherInterface $eventDispatcher, array $defaultMappingPaths = array()) {
        $this->mappingsService = $mappingsService;
        $this->wiremockMappingsPath = $wiremockMappingsPath;
        $this->eventDispatcher = $eventDispatcher;
        $this->defaultMappingPaths = $defaultMappingPaths;
    }
   
    /**
     * @BeforeScenario
     */
    public function resetWiremockMappings()
    {
        $this->mappingsService->resetMappings();
        $this->eventDispatcher->dispatch(MappingEvents::AFTER_RESET);
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
            $this->mappingsService->loadMapping($mappingFilePath);
        }
    }

    public function loadDefaultMappings()
    {
        foreach($this->defaultMappingPaths as $defaultMappingPath) {
            $this->mappingsService->loadMapping($this->wiremockMappingsPath . '/' . $defaultMappingPath['service'] . '/' . $defaultMappingPath['mapping']);
        }
    }
    
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(MappingEvents::AFTER_RESET => array('loadDefaultMappings', 0));
    }
}
