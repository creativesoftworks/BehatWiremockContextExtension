<?php

namespace CreativeSoftworks\BehatWiremockContextExtension\Mappings;

$GLOBALS['fileGetContentsReturnValue'] = '';

function file_get_contents($filename)
{
    return $GLOBALS['fileGetContentsReturnValue'];
}

namespace spec\CreativeSoftworks\BehatWiremockContextExtension\Mappings;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

class MappingsServiceSpec extends ObjectBehavior
{   
    function let(Client $client, Response $response)
    {
        $GLOBALS['fileGetContentsReturnValue'] = 'test_mapping_contents';
        $client->post(Argument::any(), Argument::any(), Argument::any())->willReturn($client);
        $client->send()->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        
        $this->beConstructedWith($client, 'base_url');
    }
    
    function it_resets_wiremock_mappings(Client $client, Response $response)
    {
        $client->post('base_url/__admin/mappings/reset')->shouldBeCalled()->willReturn($client);
        $client->send()->shouldBeCalled()->willReturn($response);
        
        $this->resetMappings();
    }
    
    function it_throws_RuntimeException_if_reset_mappings_fails(Response $response)
    {
        $response->getStatusCode()->willReturn(404);
        
        $this->shouldThrow('\RuntimeException')->duringResetMappings();
    }
    
    function it_loads_mappings_into_wiremock(Client $client, Response $response)
    {
        $GLOBALS['fileGetContentsReturnValue'] = 'test_mapping_contents';
        $client->post('base_url/__admin/mappings/new', null, 'test_mapping_contents')->shouldBeCalled()->willReturn($client);
        $client->send()->shouldBeCalled()->willReturn($response);
        $response->getStatusCode()->willReturn(201);
        
        $this->loadMapping('test/path');
    }
    
    function it_throws_InvalidArgumentException_if_mapping_file_cannot_be_read()
    {
        $GLOBALS['fileGetContentsReturnValue'] = false;
        
        $this->shouldThrow('\InvalidArgumentException')->duringLoadMapping('test/path');
    }
    
    function it_throws_RuntimeException_if_mapping_was_not_created_successfully(Response $response)
    {
        $response->getStatusCode()->willReturn(500);
        
        $this->shouldThrow('\RuntimeException')->duringLoadMapping('test/path');
    }
}
