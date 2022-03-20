<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck\Test\Command;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use ReflectionObject;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Szemul\ConsoleHealthCheck\Command\HealthCheckCommand;
use PHPUnit\Framework\TestCase;
use Szemul\ConsoleHealthCheck\HealthCheck;
use Szemul\Helper\DateHelper;
use Throwable;

class HealthCheckCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private HealthCheck|MockInterface $healthCheck;
    private DateHelper|MockInterface  $dateHelper;
    private HealthCheckCommand        $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthCheck = Mockery::mock(HealthCheck::class); // @phpstan-ignore-line
        $this->dateHelper  = Mockery::mock(DateHelper::class); // @phpstan-ignore-line
        $this->sut         = new HealthCheckCommand($this->healthCheck, $this->dateHelper); // @phpstan-ignore-line
    }

    public function testExecuteWithHealthyFile(): void
    {
        $input  = $this->getInput(10);
        $output = $this->getOutput();

        $this->expectHealthChecked(true, 10)
            ->expectOutputLine($output, 'The process is healthy');

        $this->assertSame(0, $this->runExecute($input, $output));
    }

    public function testExecuteWithUnhealthyButValidFile(): void
    {
        $input             = $this->getInput(10, 123);
        $output            = $this->getOutput();
        $lastHealthCheckAt = CarbonImmutable::create(2022, 3, 20, 1, 2, 3)->setMicro(0);

        $this->expectHealthChecked(false, 10)
            ->expectLastHealthCheckDateRetrieved($lastHealthCheckAt)
            ->expectOutputLine(
                $output,
                'The process is not healthy, the last update was at ' . $lastHealthCheckAt->toAtomString(),
            );

        $this->assertSame(123, $this->runExecute($input, $output));
    }

    public function testExecuteWithUnhealthyAndInvalidFile(): void
    {
        $input             = $this->getInput(10, 123);
        $output            = $this->getOutput();

        $this->expectHealthChecked(false, 10)
            ->expectLastHealthCheckDateRetrievedWithException(new RuntimeException('test'))
            ->expectOutputLine(
                $output,
                'The process is not healthy and the check file does not exist or contains invalid data',
            );

        $this->assertSame(123, $this->runExecute($input, $output));
    }

    private function getInput(int $thresholdSeconds, int $errorExitCode = 1): InputInterface|MockInterface
    {
        $input = Mockery::mock(InputInterface::class);

        $input->shouldReceive('getOption') // @phpstan-ignore-line
        ->with('threshold-seconds')
            ->andReturn((string)$thresholdSeconds);

        $input->shouldReceive('getOption') // @phpstan-ignore-line
        ->with('error-exit-code')
            ->andReturn((string)$errorExitCode);

        return $input; // @phpstan-ignore-line
    }

    private function getOutput(): OutputInterface|MockInterface
    {
        return Mockery::mock(OutputInterface::class); // @phpstan-ignore-line
    }

    private function runExecute(InputInterface $input, OutputInterface $output): int
    {
        $method = (new ReflectionObject($this->sut))->getMethod('execute');

        $method->setAccessible(true);

        return $method->invoke($this->sut, $input, $output);
    }

    private function expectHealthChecked(bool $isHealthy, int $thresholdSeconds): static
    {
        $orignal = CarbonImmutable::now();

        $this->dateHelper->shouldReceive('getCurrentTime')->andReturn($orignal);

        // @phpstan-ignore-next-line
        $this->healthCheck->shouldReceive('checkIsHealthy')
            ->once()
            ->with(Mockery::on(function (CarbonInterface $actual) use ($orignal, $thresholdSeconds) {
                $expected = $orignal->subSeconds($thresholdSeconds);
                $this->assertSame($expected->timestamp, $actual->timestamp);

                return true;
            }))
            ->andReturn($isHealthy);

        return $this;
    }

    private function expectOutputLine(OutputInterface|MockInterface $output, string $line): static
    {
        $output->shouldReceive('writeln')->once()->with($line); // @phpstan-ignore-line

        return $this;
    }

    private function expectLastHealthCheckDateRetrieved(CarbonInterface $carbon): static
    {
        // @phpstan-ignore-next-line
        $this->healthCheck->shouldReceive('getLastHealthCheckDate')->once()->withNoArgs()->andReturn($carbon);

        return $this;
    }

    private function expectLastHealthCheckDateRetrievedWithException(Throwable $exception): static
    {
        // @phpstan-ignore-next-line
        $this->healthCheck->shouldReceive('getLastHealthCheckDate')->once()->withNoArgs()->andThrow($exception);

        return $this;
    }
}
