<?php

namespace tests\Unit;

use \PHPUnit\Framework\TestCase;
use src\Calculator;
use src\Commands\CommandInterface;

class CalculatorTest extends TestCase
{
    /**
     * @var Calculator
     */
    private $calc;

    /**
     * TODO: explain difference between setUp() and tearDown()
     * TODO: what difference between setUp() and setUpBeforeClass()
     *
     * @see http://phpunit.readthedocs.io/en/7.1/fixtures.html#more-setup-than-teardown
     */
    public function setUp()
    {
        $this->calc = new Calculator();
    }

    /**
     * TODO: Which methods should be mocked for Command?
     *
     * @see https://phpunit.readthedocs.io/en/7.1/test-doubles.html
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getCommandMock()
    {
        return $this->getMockBuilder(CommandInterface::class)
            ->getMock();
    }

    /**
     * TODO: Check whether intents = []
     * TODO: Check whether value = 0.0
     *
     * @see PHPUnit :: assertAttributeEquals
     */
    public function testInitialCalcState()
    {
        $this->calc->init(7);
        $this->assertAttributeEquals(7, 'value', $this->calc);
    }

    /**
     * TODO: Check name exception
     *
     * @covers \src\Calculator::addCommand()
     */
    public function testAddCommandNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calc->addCommand(null, $this->getCommandMock());
    }

    /**
     * TODO: Check whether command is in the commands array
     *
     * @covers \src\Calculator::addCommand()
     */
    public function testAddCommandPositive()
    {
        $this->calc->addCommand('valid', $this->getCommandMock());
        $this->assertAttributeEquals(['valid' => $this->getCommandMock()], 'commands', $this->calc);
    }

    /**
     * TODO: Check whether intents = []
     * TODO: Check whether value = expected
     *
     * @see    PHPUnit :: assertAttributeEquals
     *
     * @covers \src\Calculator::init()
     */
    public function testInit()
    {
        $this->calc->init();

        $this->assertAttributeEquals(0, 'value', $this->calc);
        $this->assertAttributeEquals([], 'intents', $this->calc);
    }

    /**
     * TODO: Check hasCommand exception
     *
     * @see    PHPUnit :: dataProvider
     *
     * @covers \src\Calculator::compute()
     */
    public function testComputeNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calc->init(31)
            ->compute('doesn`t exist', 12);
    }

    /**
     * TODO: Check whether command and arguments have appeared in the intents array
     *
     * @see    PHPUnit :: assertAttributeEquals
     *
     * @covers \src\Calculator::compute()
     */
    public function testComputePositive()
    {
        $command = $this->getCommandMock();
        $this->calc->addCommand('+', $command);
        $this->calc->init(42)
            ->compute('+', 42);

        $this->assertAttributeEquals([[$command, [42]]], 'intents', $this->calc);
    }

    /**
     * TODO: Check that command was executed
     *
     * Mock command`s execute method and check whether it was called at least once with the correct arguments
     *
     * @see https://phpunit.readthedocs.io/en/7.1/test-doubles.html
     *
     * @covers \src\Calculator::getResult()
     */
    public function testGetResultPositive()
    {
        $command = $this->getCommandMock();
        $command->expects($this->once())->method('execute');
        $this->calc->addCommand('+', $command);
        $this->calc->init(42)
            ->compute('+', 42)
            ->getResult();
    }

    /**
     * TODO: Check that command was executed with exception
     *
     * Mock command`s execute method so that it returns exception and check whether it was thrown
     *
     * @see https://phpunit.readthedocs.io/en/7.1/test-doubles.html
     *
     * @covers \src\Calculator::getResult()
     */
    public function testGetResultNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $command = $this->getCommandMock();
        $command->expects($this->once())->method('execute')->will($this->throwException(new \InvalidArgumentException()));
        $this->calc->addCommand('+', $command);
        $this->calc->init(42)
            ->compute('+', 20, 22, 22)
            ->getResult();
    }

    /**
     * TODO: Check whether the last item in the intents array was duplicated
     *
     * @covers \src\Calculator::replay()
     */
    public function testReplay()
    {
        $command = $this->getCommandMock();
        $this->calc->addCommand('+', $command);
        $this->calc->init(3)
            ->compute('+', 4)
            ->replay();

        $this->assertAttributeEquals([[$command, [4]], [$command, [4]],], 'intents', $this->calc);
    }

    /**
     * TODO: Check whether the last item was removed from intents array
     *
     * @covers \src\Calculator::undo()
     */
    public function testUndo()
    {
        $command = $this->getCommandMock();
        $this->calc->addCommand('-', $command);
        $this->calc->init(5)
            ->compute('-', 2)
            ->undo();

        $this->assertAttributeEquals([], 'intents', $this->calc);
    }

    /**
     * TODO: what difference between tearDown() and tearDownAfterClass()
     *
     * @see http://phpunit.readthedocs.io/en/7.1/fixtures.html#more-setup-than-teardown
     */
    public function tearDown()
    {
        unset($this->calc);
    }
}
