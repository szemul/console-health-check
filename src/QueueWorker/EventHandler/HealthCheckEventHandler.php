<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck\QueueWorker\EventHandler;

use Szemul\ConsoleHealthCheck\HealthCheck;
use Szemul\Queue\Message\MessageInterface;
use Szemul\QueueWorker\EventHandler\CommandEventHandlerInterface;
use Szemul\QueueWorker\EventHandler\WorkerEventHandlerInterface;
use Throwable;

class HealthCheckEventHandler implements CommandEventHandlerInterface, WorkerEventHandlerInterface
{

    public function __construct(protected HealthCheck $healthCheck)
    {
    }

    public function handleBeforeLoop(): void
    {
        // Noop
    }

    public function handleIterationStart(): void
    {
        $this->healthCheck->update();
    }

    public function handleIterationComplete(): void
    {
        $this->healthCheck->update();
    }

    public function handleCommandFinally(): void
    {
        // Noop
    }

    public function handleCommandException(Throwable $e): void
    {
        // Noop
    }

    public function handleCommandInterrupted(): void
    {
        // Noop
    }

    public function handleCommandFinished(): void
    {
        // Noop
    }

    public function handleSignalReceived(int $signal): void
    {
        // Noop
    }

    public function handleInterrupt(): void
    {
        // Noop
    }

    public function handleMessageReceived(MessageInterface $message): void
    {
        $this->healthCheck->update();
    }

    public function handleMessageProcessed(MessageInterface $message): void
    {
        $this->healthCheck->update();
    }

    public function handleWorkerException(Throwable $e): void
    {
        // Noop
    }

    public function handleWorkerFinally(): void
    {
        // Noop
    }

}
