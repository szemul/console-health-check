<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck\Test;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use RuntimeException;
use Szemul\ConsoleHealthCheck\HealthCheck;
use PHPUnit\Framework\TestCase;
use Szemul\Helper\DateHelper;

class HealthCheckTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const TEST_PATH = '/tmp/console-health-check';

    private DateHelper|MockInterface $dateHelper;
    private HealthCheck              $sut;

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(self::TEST_PATH)) {
            unlink(self::TEST_PATH);
        }

        $this->dateHelper = Mockery::mock(DateHelper::class);
        $this->sut        = new HealthCheck($this->dateHelper, self::TEST_PATH); // @phpstan-ignore-line
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (file_exists(self::TEST_PATH)) {
            unlink(self::TEST_PATH);
        }
    }

    public function testUpdate(): void
    {
        $carbon = $this->expectCurrentTimeRetrieved();

        $this->sut->update();

        $this->assertFileExists(self::TEST_PATH);
        $this->assertStringEqualsFile(self::TEST_PATH, (string)$carbon->timestamp);
    }

    public function testGetLastHealthCheckDateWithNoFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The check file does not exist');
        $this->sut->getLastHealthCheckDate();
    }

    public function testGetLastHealthCheckDateWithInvalidDataInFile(): void
    {
        file_put_contents(self::TEST_PATH, 'foo');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The check file does not contain a timestamp. Contents: foo');
        $this->sut->getLastHealthCheckDate();
    }

    public function testGetLastHealthCheckDateWithValidDataInFile(): void
    {
        $carbon = CarbonImmutable::now();
        file_put_contents(self::TEST_PATH, (string)$carbon->timestamp);

        $this->assertSame($carbon->timestamp, $this->sut->getLastHealthCheckDate()->timestamp);
    }

    public function testCheckIsHealthyWithNoFile(): void
    {
        $this->assertFalse($this->sut->checkIsHealthy(CarbonImmutable::now()));
    }

    public function testCheckIsHealthyWithValidExpiredFile(): void
    {
        $carbon = CarbonImmutable::create(2022, 3, 20, 1, 2, 3);
        file_put_contents(self::TEST_PATH, $carbon->timestamp);
        $this->assertFalse($this->sut->checkIsHealthy($carbon));
    }

    public function testCheckIsHealthyWithValidNotExpiredFile(): void
    {
        $carbon = CarbonImmutable::create(2022, 3, 20, 1, 2, 3);
        file_put_contents(self::TEST_PATH, $carbon->timestamp);
        $this->assertTrue($this->sut->checkIsHealthy($carbon->subSecond()));
    }

    private function expectCurrentTimeRetrieved(): CarbonInterface
    {
        $carbon = CarbonImmutable::create(2022, 3, 20, 1, 2, 3)->setMicro(456789);

        $this->dateHelper->shouldReceive('getCurrentTime')->withNoArgs()->andReturn($carbon); // @phpstan-ignore-line

        return $carbon;
    }
}
