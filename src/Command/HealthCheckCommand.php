<?php
declare(strict_types=1);

namespace Szemul\ConsoleHealthCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Szemul\ConsoleHealthCheck\HealthCheck;
use Szemul\Helper\DateHelper;

class HealthCheckCommand extends Command
{
    protected static $defaultName        = 'health:check-console';
    protected static $defaultDescription = 'Checks that a long running command has updated its health check file recently';

    public function __construct(
        protected HealthCheck $healthCheck,
        protected DateHelper $dateHelper,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption(
            'threshold-seconds',
            't',
            InputOption::VALUE_REQUIRED,
            'The number of seconds to allow for the timestamp to be updated',
        );

        $this->addOption(
            'error-exit-code',
            'e',
            InputOption::VALUE_REQUIRED,
            'The exit code to return with in case of an error',
            1,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $thresholdSeconds = $input->getOption('threshold-seconds');
        $errorExitCode    = max(1, (int)$input->getOption('error-exit-code'));

        $cutOffDate = $this->dateHelper->getCurrentTime()->subSeconds($thresholdSeconds);

        if ($this->healthCheck->checkIsHealthy($cutOffDate)) {
            $output->writeln('The process is healthy');

            return 0;
        }

        try {
            $date = $this->healthCheck->getLastHealthCheckDate();
            $output->writeln('The process is not healthy, the last update was at ' . $date->toAtomString());
        } catch (\RuntimeException|\InvalidArgumentException) {
            $output->writeln('The process is not healthy and the check file does not exist or contains invalid data');
        }

        return $errorExitCode;
    }
}
