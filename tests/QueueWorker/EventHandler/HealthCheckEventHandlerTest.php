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

        $this->healthCheck = Mockery::mock(HealthCheck::class);
        $this->sut         = new HealthCheckEventHandler($this->healthCheck); // @phpstan-ignore-line
    }

    public function testHandleBeforeLoop(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleBeforeLoop();
    }

    public function testHandleCommandException(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleCommandException(new RuntimeException());
    }

    public function testHandleCommandFinally(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleCommandFinally();
    }

    public function testHandleCommandFinished(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleCommandFinished();
    }

    public function testHandleCommandInterrupted(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleCommandInterrupted();
    }

    public function testHandleInterrupt(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleInterrupt();
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
        $this->expectNotToPerformAssertions();
        $this->sut->handleSignalReceived(SIGINT);
    }

    public function testHandleWorkerException(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleWorkerException(new RuntimeException());
    }

    public function testHandleWorkerFinally(): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->handleWorkerFinally();
    }

    private function expectHealthcheckUpdated(): static
    {
        $this->healthCheck->shouldReceive('update')->once()->withNoArgs(); // @phpstan-ignore-line

        return $this;
    }
}
