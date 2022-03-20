<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;
use RuntimeException;
use Szemul\Helper\DateHelper;

class HealthCheck
{

    public function __construct(
        protected DateHelper $dateHelper,
        protected string $checkFilePath = '/tmp/console-health-check',
    ) {
        if (file_exists($this->checkFilePath)) {
            if (!is_writable($this->checkFilePath)) {
                throw new InvalidArgumentException(
                    'The check file file at path at ' . $this->checkFilePath . ' is not writable',
                );
            }
        } elseif (!file_exists(dirname($this->checkFilePath))) {
            if (!mkdir(dirname($this->checkFilePath), recursive: true)) {
                throw new InvalidArgumentException(
                    'Failed to create parent directory for check file at ' . $this->checkFilePath,
                );
            }
        } elseif (!is_writable(dirname($this->checkFilePath))) {
            throw new InvalidArgumentException(
                'The parent directory for the check file at ' . $this->checkFilePath . ' is not writable',
            );
        }
    }

    public function update(): void
    {
        file_put_contents($this->checkFilePath, $this->dateHelper->getCurrentTime()->timestamp);
    }

    public function getLastHealthCheckDate(): CarbonInterface
    {
        if (!file_exists($this->checkFilePath)) {
            throw new RuntimeException('The check file does not exist');
        }

        $timestamp = file_get_contents($this->checkFilePath);

        if (!is_numeric($timestamp)) {
            throw new RuntimeException('The check file does not contain a timestamp. Contents: ' . $timestamp);
        }

        return CarbonImmutable::createFromTimestamp($timestamp);
    }

    public function checkIsHealthy(CarbonInterface $cutoffDate): bool
    {
        try {
            $checkTime = $this->getLastHealthCheckDate();
        } catch (RuntimeException|InvalidFormatException) {
            return false;
        }

        return $checkTime->isAfter($cutoffDate);
    }
}
