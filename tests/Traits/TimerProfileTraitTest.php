<?php namespace Orchestra\Debug\TestCase\Traits;

use Mockery as m;
use Orchestra\Debug\Traits\TimerProfileTrait;

class TimerProfileTraitTest extends \PHPUnit_Framework_TestCase
{
    use TimerProfileTrait;

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testTimerMethod()
    {
        $this->timers  = [];
        $this->monolog = $monolog = m::mock('\Monolog\Logger');

        $monolog->shouldReceive('addInfo')->once()->with(m::type('String'));

        $this->assertEquals('foo', $this->time('foo'));
        $this->assertNull($this->timeEnd('foo'));

        $this->assertNotEquals('foo', $this->time('foo'));
    }

    /**
     * @test
     */
    public function testTimerNameCannotBeUsedTwice()
    {
        $this->timers = [];

        $this->assertEquals('foobar', $this->time('foobar'));
        $this->assertNotEquals('foobar', $this->time('foobar'));
    }

    /**
     * @test
     */
    public function testTimerMethodWithoutCallingStartTime()
    {
        $this->timers  = [];
        $this->monolog = $monolog = m::mock('\Monolog\Logger');

        defined('LARAVEL_START') || define('LARAVEL_START', microtime(true));

        $monolog->shouldReceive('addInfo')->once()->with(m::type('String'));

        $this->assertNull($this->timeEnd('foo'));
    }
}
