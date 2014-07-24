<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

$GLOBALS['fileGetContentsReturnValue'] = '';

function file_get_contents($filename)
{
    return $GLOBALS['fileGetContentsReturnValue'];
}

namespace spec\CreativeSoftworks\BehatWiremockContextExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use CreativeSoftworks\BehatWiremockContextExtension\Event\MappingEvents;

class WiremockContextSpec extends ObjectBehavior
{   
    function let(Client $client, Response $response, TableNode $table, EventDispatcherInterface $eventDispatcher)
    {
        $GLOBALS['fileGetContentsReturnValue'] = 'test_mapping_contents';
        $client->post(Argument::any(), Argument::any(), Argument::any())->willReturn($client);
        $client->send()->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        
        $table->getHash()->willReturn(array(array('service' => 'test_service', 'mapping' => 'test_mapping')));
        
        $this->beConstructedWith($client, 'base_url', 'mappings_path', $eventDispatcher);
    }
    
    function it_resets_wiremock_mappings(Client $client, Response $response)
    {
        $client->post('base_url/__admin/mappings/reset')->shouldBeCalled()->willReturn($client);
        $client->send()->shouldBeCalled()->willReturn($response);
        
        $this->resetWiremockMappings();
    }
    
    function it_throws_RuntimeException_if_reset_mappings_fails(Response $response)
    {
        $response->getStatusCode()->willReturn(404);
        
        $this->shouldThrow('\RuntimeException')->duringResetWiremockMappings();
    }
    
    function it_dispatches_after_reset_event_if_reset_was_successful(Response $response, EventDispatcherInterface $eventDispatcher)
    {
        $response->getStatusCode()->willReturn(200);
        $eventDispatcher->dispatch(MappingEvents::AFTER_RESET)->shouldBeCalled();
        
        $this->resetWiremockMappings();
    }
    
    function it_submits_mappings_to_wiremock(Client $client, TableNode $table, Response $response)
    {
        $GLOBALS['fileGetContentsReturnValue'] = 'test_mapping_contents';
        $table->getHash()->willReturn(array(array('service' => 'test_service', 'mapping' => 'test_mapping')));
        $client->post('base_url/__admin/mappings/new', null, 'test_mapping_contents')->shouldBeCalled()->willReturn($client);
        $client->send()->shouldBeCalled()->willReturn($response);
        $response->getStatusCode()->willReturn(201);
        
        $this->theFollowingServicesExist($table);
    }
    
    function it_throws_IvalidArgumentException_if_table_hash_does_not_contain_serice_or_mapping_columns(TableNode $table)
    {
        $table->getHash()->willReturn(array(array('something' => 'something else')));
        
        $this->shouldThrow('\InvalidArgumentException')->duringTheFollowingServicesExist($table);
    }
    
    function it_throws_InvalidArgumentException_if_mapping_file_cannot_be_read(TableNode $table)
    {
        $GLOBALS['fileGetContentsReturnValue'] = false;
        
        $this->shouldThrow('\InvalidArgumentException')->duringTheFollowingServicesExist($table);
    }
    
    function it_throws_RuntimeException_if_mapping_was_not_created_successfully(TableNode $table, Response $response)
    {
        $response->getStatusCode()->willReturn(500);
        
        $this->shouldThrow('\RuntimeException')->duringTheFollowingServicesExist($table);
    }
}
