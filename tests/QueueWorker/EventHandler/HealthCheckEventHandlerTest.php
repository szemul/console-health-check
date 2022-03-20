<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck\Test\QueueWorker\EventHandler;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use RuntimeException;
use Szemul\ConsoleHealthCheck\HealthCheck;
use Szemul\ConsoleHealthCheck\QueueWorker\EventHandler\HealthCheckEventHandler;
use PHPUnit\Framework\TestCase;
use Szemul\Queue\Message\MessageInterface;

class HealthCheckEventHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private HealthCheck|MockInterface $healthCheck;
    private HealthCheckEventHandler   $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthCheck = Mockery::mock(HealthCheck::class); // @phpstan-ignore-line
        $this->sut         = new HealthCheckEventHandler($this->healthCheck); // @phpstan-ignore-line
    }

    public function testHandleBeforeLoop(): void
    {
        $this->sut->handleBeforeLoop();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleCommandException(): void
    {
        $this->sut->handleCommandException(new RuntimeException());
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleCommandFinally(): void
    {
        $this->sut->handleCommandFinally();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleCommandFinished(): void
    {
        $this->sut->handleCommandFinished();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleCommandInterrupted(): void
    {
        $this->sut->handleCommandInterrupted();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleInterrupt(): void
    {
        $this->sut->handleInterrupt();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleIterationComplete(): void
    {
        $this->expectHealthcheckUpdated();
        $this->sut->handleIterationComplete();
    }

    public function testHandleIterationStart(): void
    {
        $this->expectHealthcheckUpdated();
        $this->sut->handleIterationStart();
    }

    public function testHandleMessageProcessed(): void
    {
        $this->expectHealthcheckUpdated();
        $this->sut->handleMessageProcessed(Mockery::mock(MessageInterface::class)); // @phpstan-ignore-line
    }

    public function testHandleMessageReceived(): void
    {
        $this->expectHealthcheckUpdated();
        $this->sut->handleMessageReceived(Mockery::mock(MessageInterface::class)); // @phpstan-ignore-line
    }

    public function testHandleSignalReceived(): void
    {
        $this->sut->handleSignalReceived(SIGINT);
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleWorkerException(): void
    {
        $this->sut->handleWorkerException(new RuntimeException());
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    public function testHandleWorkerFinally(): void
    {
        $this->sut->handleWorkerFinally();
        // Noop assert,this method should do nothing
        $this->assertTrue(true);
    }

    private function expectHealthcheckUpdated(): static
    {
        $this->healthCheck->shouldReceive('update')->once()->withNoArgs(); // @phpstan-ignore-line

        return $this;
    }
}
