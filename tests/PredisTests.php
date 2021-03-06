<?php

use League\Flysystem\Cache\Predis;
use Predis\Client;

class PredisTests extends PHPUnit_Framework_TestCase
{
    public function testLoadFail()
    {
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('get', array('flysystem'))->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->andReturn(null);
        $cache = new Predis($client);
        $cache->load();
        $this->assertFalse($cache->isComplete('', false));
    }

    public function testLoadSuccess()
    {
        $response = json_encode(array(array(), array('' => true)));
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('get', array('flysystem'))->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->andReturn($response);
        $cache = new Predis($client);
        $cache->load();
        $this->assertTrue($cache->isComplete('', false));
    }

    public function testSave()
    {
        $data = json_encode(array(array(), array()));
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('set', array('flysystem', $data))->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->once();
        $cache = new Predis($client);
        $cache->save();
    }

    public function testSaveWithExpire()
    {
        $data = json_encode(array(array(), array()));
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('set', array('flysystem', $data))->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->once();
        $expireCommand = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('expire', array('flysystem', 20))->once()->andReturn($expireCommand);
        $client->shouldReceive('executeCommand')->with($expireCommand)->once();
        $cache = new Predis($client, 'flysystem', 20);
        $cache->save();
    }
}
