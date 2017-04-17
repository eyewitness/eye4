<?php

use Eyewitness\Eye\Eye;

class EyeTest extends TestCase
{
    protected $eye;

    public function setUp()
    {
        parent::setUp();

        $this->eye = new Eye;
    }

    public function testEyeIsConstructedCorrectly()
    {
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Scheduler::class, $this->eye->scheduler());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Database::class, $this->eye->database());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Request::class, $this->eye->request());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Server::class, $this->eye->server());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Queue::class, $this->eye->queue());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Email::class, $this->eye->email());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Disk::class, $this->eye->disk());
        $this->assertInstanceOf(Eyewitness\Eye\Witness\Log::class, $this->eye->log());
        $this->assertInstanceOf(Eyewitness\Eye\Api\Api::class, $this->eye->api());
    }

    public function testConstsAreSet()
    {
        $this->assertEquals('QUEUE_CONNECTION_PLACEHOLDER', $this->eye::QUEUE_CONNECTION_PLACEHOLDER);
        $this->assertEquals('QUEUE_TUBE_PLACEHOLDER', $this->eye::QUEUE_TUBE_PLACEHOLDER);
        $this->assertEquals('SECRET_KEY_PLACEHOLDER', $this->eye::SECRET_KEY_PLACEHOLDER);
        $this->assertEquals('APP_TOKEN_PLACEHOLDER', $this->eye::APP_TOKEN_PLACEHOLDER);
        $this->assertTrue($this->eye::EYE_VERSION !== '');
    }

    public function testConfigCorrectDetectsIfValid()
    {
        $this->app['config']['eye::app_token'] = '';
        $this->app['config']['eye::secret_key'] = '';
        $this->assertFalse($this->eye->checkConfig());

        $this->app['config']['eye::app_token'] = null;
        $this->app['config']['eye::secret_key'] = null;
        $this->assertFalse($this->eye->checkConfig());

        $this->app['config']['eye::app_token'] = 'APP_TOKEN_PLACEHOLDER';
        $this->app['config']['eye::secret_key'] = 'SECRET_KEY_PLACEHOLDER';
        $this->assertFalse($this->eye->checkConfig());

        $this->app['config']['eye::app_token'] = 'blah';
        $this->app['config']['eye::secret_key'] = 'blah';
        $this->assertTrue($this->eye->checkConfig());
    }
}
