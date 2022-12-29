<?php

declare (strict_types=1);
namespace Rector\Core\Console\Command;

use RectorPrefix202212\Clue\React\NDJson\Decoder;
use RectorPrefix202212\Clue\React\NDJson\Encoder;
use RectorPrefix202212\React\EventLoop\StreamSelectLoop;
use RectorPrefix202212\React\Socket\ConnectionInterface;
use RectorPrefix202212\React\Socket\TcpConnector;
use Rector\Core\Util\MemoryLimiter;
use Rector\Parallel\WorkerRunner;
use RectorPrefix202212\Symfony\Component\Console\Input\InputInterface;
use RectorPrefix202212\Symfony\Component\Console\Output\OutputInterface;
use RectorPrefix202212\Symplify\EasyParallel\Enum\Action;
use RectorPrefix202212\Symplify\EasyParallel\Enum\ReactCommand;
/**
 * Inspired at: https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 * https://github.com/phpstan/phpstan-src/blob/c471c7b050e0929daf432288770de673b394a983/src/Command/WorkerCommand.php
 *
 * ↓↓↓
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2
 */
final class WorkerCommand extends \Rector\Core\Console\Command\AbstractProcessCommand
{
    /**
     * @readonly
     * @var \Rector\Parallel\WorkerRunner
     */
    private $workerRunner;
    /**
     * @readonly
     * @var \Rector\Core\Util\MemoryLimiter
     */
    private $memoryLimiter;
    public function __construct(WorkerRunner $workerRunner, MemoryLimiter $memoryLimiter)
    {
        $this->workerRunner = $workerRunner;
        $this->memoryLimiter = $memoryLimiter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('worker');
        $this->setDescription('(Internal) Support for parallel process');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $configuration = $this->configurationFactory->createFromInput($input);
        $this->memoryLimiter->adjust($configuration);
        $streamSelectLoop = new StreamSelectLoop();
        $parallelIdentifier = $configuration->getParallelIdentifier();
        $tcpConnector = new TcpConnector($streamSelectLoop);
        $promise = $tcpConnector->connect('127.0.0.1:' . $configuration->getParallelPort());
        $promise->then(function (ConnectionInterface $connection) use($parallelIdentifier, $configuration) : void {
            $inDecoder = new Decoder($connection, \true, 512, \JSON_INVALID_UTF8_IGNORE);
            $outEncoder = new Encoder($connection, \JSON_INVALID_UTF8_IGNORE);
            // handshake?
            $outEncoder->write([ReactCommand::ACTION => Action::HELLO, ReactCommand::IDENTIFIER => $parallelIdentifier]);
            $this->workerRunner->run($outEncoder, $inDecoder, $configuration);
        });
        $streamSelectLoop->run();
        return self::SUCCESS;
    }
}
