<?php
namespace Minphp\Session\Tests\Unit\Session;

use Minphp\Session\Session;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Minphp\Session\Session
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setOptions
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Minphp\Session\Session', new Session());
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     */
    public function testConstructWithOptions()
    {
        $options = [
            'name' => 'my-session-name'
        ];
        $this->assertInstanceOf('\Minphp\Session\Session', new Session(null, $options));

        $this->assertEquals($options['name'], ini_get('session.name'));
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::start
     * @covers ::hasStarted
     *
     * @runInSeparateProcess
     */
    public function testStart()
    {
        $session = new Session();

        $this->assertFalse($session->hasStarted());
        $this->assertTrue($session->start());
        $this->assertTrue($session->hasStarted());
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::save
     * @covers ::hasStarted
     */
    public function testSave()
    {
        $session = new Session();
        $session->save();

        $this->assertFalse($session->hasStarted());
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::start
     * @covers ::hasStarted
     * @covers ::regenerate
     *
     * @runInSeparateProcess
     */
    public function testRegenerate()
    {
        $session = new Session();
        $this->assertFalse($session->regenerate());

        $session->start();
        $lifetime = 100;
        $this->assertTrue($session->regenerate(false, $lifetime));
        $this->assertEquals($lifetime, ini_get('session.cookie_lifetime'));
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::getId
     * @covers ::setId
     * @covers ::hasStarted
     */
    public function testId()
    {
        $sessionId = 'sessionId';
        $session = new Session();
        $this->assertNotNull($session->getId());

        $session->setId($sessionId);
        $this->assertEquals($sessionId, $session->getId());
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::getId
     * @covers ::setId
     * @covers ::hasStarted
     * @expectedException \LogicException
     *
     * @runInSeparateProcess
     */
    public function testIdException()
    {
        $session = new Session();
        $session->start();
        $session->setId('id-that-cant-be-set');
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::getName
     * @covers ::setName
     * @covers ::hasStarted
     */
    public function testName()
    {
        $sessionName = 'sessionName';
        $session = new Session();
        $this->assertNotNull($session->getName());

        $session->setName($sessionName);
        $this->assertEquals($sessionName, $session->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::getName
     * @covers ::setName
     * @covers ::hasStarted
     * @expectedException \LogicException
     *
     * @runInSeparateProcess
     */
    public function testNameException()
    {
        $session = new Session();
        $session->start();
        $session->setName('name-that-cant-be-set');
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::read
     * @covers ::write
     */
    public function testReadWrite()
    {
        $key = 'value';
        $value = 'something';

        $session = new Session();
        $this->assertEquals('', $session->read($key));
        $session->write($key, $value);
        $this->assertEquals($value, $session->read($key));
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::read
     * @covers ::write
     * @covers ::clear
     */
    public function testClear()
    {
        $session = new Session();
        $session->write('key1', 'value1');
        $session->write('key2', 'value2');
        $session->write('key3', 'value3');

        $session->clear('key1');

        $this->assertArrayNotHasKey('key1', $_SESSION);
        $this->assertArrayHasKey('key2', $_SESSION);

        $session->clear();
        $this->assertEmpty($_SESSION);
    }


    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::cookie
     */
    public function testCookie()
    {
        $this->markTestSkipped('Cannot test whether a cookie was created.');
    }
}
