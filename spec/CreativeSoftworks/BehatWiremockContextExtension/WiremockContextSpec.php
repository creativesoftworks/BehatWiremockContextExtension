<?php

namespace spec\CreativeSoftworks\BehatWiremockContextExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use CreativeSoftworks\BehatWiremockContextExtension\Event\MappingEvents;
use CreativeSoftworks\BehatWiremockContextExtension\Mappings\MappingsService;

class WiremockContextSpec extends ObjectBehavior
{   
    private $defaultMappings = array('service/mapping', 'anotherService/anotherMapping');
    
    function let(MappingsService $mappingsService, TableNode $table, EventDispatcherInterface $eventDispatcher)
    { 
        $table->getHash()->willReturn(array(array('service' => 'test_service', 'mapping' => 'test_mapping')));
        
        $this->beConstructedWith($mappingsService, 'mappings_path', $eventDispatcher, $this->defaultMappings);
    }
    
    function it_resets_wiremock_mappings(MappingsService $mappingsService)
    {
        $mappingsService->resetMappings()->shouldBeCalled();
        
        $this->resetWiremockMappings();
    }
    
    function it_dispatches_after_reset_event_if_reset_was_successful(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch(MappingEvents::AFTER_RESET)->shouldBeCalled();
        
        $this->resetWiremockMappings();
    }
    
    function it_submits_mappings_to_wiremock(MappingsService $mappingsService, TableNode $table)
    {
        $table->getHash()->willReturn(array(array('service' => 'test_service', 'mapping' => 'test_mapping')));
        $mappingsService->loadMapping('mappings_path/test_service/test_mapping')->shouldBeCalled();
        
        $this->theFollowingServicesExist($table);
    }
    
    function it_throws_InvalidArgumentException_if_table_hash_does_not_contain_serice_or_mapping_columns(TableNode $table)
    {
        $table->getHash()->willReturn(array(array('something' => 'something else')));
        
        $this->shouldThrow('\InvalidArgumentException')->duringTheFollowingServicesExist($table);
    }
    
    function it_loads_default_mappings_into_wiremock(MappingsService $mappingsService)
    {
        $mappingsService->loadMapping('mappings_path/service/mapping')->shouldBeCalled();
        $mappingsService->loadMapping('mappings_path/anotherService/anotherMapping')->shouldBeCalled();
        
        $this->loadDefaultMappings($this->defaultMappings);
    }
}
