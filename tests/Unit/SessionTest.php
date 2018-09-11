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
     * @covers ::hasStarted
     * @covers ::save
     */
    public function testConstruct()
    {
        $session = new Session();
        $this->assertInstanceOf('\Minphp\Session\Session', $session);

        // Clear the session so it does not affect other tests
        $session->save();
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::hasStarted
     * @covers ::save
     */
    public function testConstructWithOptions()
    {
        $options = [
            'name' => 'my-session-name'
        ];
        $session = new Session(null, $options);

        $this->assertInstanceOf('\Minphp\Session\Session', $session);

        $this->assertEquals($options['name'], ini_get('session.name'));

        // Clear the session so it does not affect other tests
        $session->save();
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::start
     * @covers ::hasStarted
     * @covers ::save
     *
     * @runInSeparateProcess
     */
    public function testStart()
    {
        $session = new Session();

        $this->assertTrue($session->start());
        $this->assertTrue($session->hasStarted());

        // Close the session
        $session->save();

        $this->assertFalse($session->hasStarted());
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
     * @expectedException \LogicException
     *
     * @runInSeparateProcess
     */
    public function testSetOptionsException()
    {
        // Start the session
        $session = new Session();
        $session->start();

        // Setting options after the session has been started throws an exception
        $options = [
            'name' => 'my-session-name'
        ];
        $session->setOptions($options);
    }

    /**
     * @covers ::__construct
     * @covers ::setOptions
     * @covers ::start
     * @covers ::hasStarted
     * @covers ::regenerate
     * @covers ::save
     *
     * @runInSeparateProcess
     */
    public function testRegenerate()
    {
        $session = new Session();

        // Make sure the session is closed first
        $session->save();

        // Cannot regenerate with no active session
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
     * @covers ::hasStarted
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
     * @covers ::hasStarted
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
