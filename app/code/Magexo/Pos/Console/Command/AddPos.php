<?php
namespace Magexo\Pos\Console\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magexo\Pos\Model\PosFactory;
use Magento\Framework\Console\Cli;

class AddPos extends Command
{
    const INPUT_KEY_NAME = 'name';
    const INPUT_KEY_ADDRESS = 'address';
    const INPUT_KEY_IS_AVAILABLE = 'is_available';

    private $posFactory;
    private $logger;

    public function __construct(PosFactory $posFactory, LoggerInterface $logger)
    {
        $this->posFactory = $posFactory;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magexo:pos:add')
            ->addArgument(
                self::INPUT_KEY_NAME,
                InputArgument::REQUIRED,
                'Pos name'
            )
            ->addArgument(
                self::INPUT_KEY_ADDRESS,
                InputArgument::REQUIRED,
                'Pos address'
            )
            ->addArgument(
                self::INPUT_KEY_IS_AVAILABLE,
                InputArgument::REQUIRED,
                'Pos available'
            )
        ;
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $item = $this->posFactory->create();
        $item->setName($input->getArgument(self::INPUT_KEY_NAME));
        $item->setAddress($input->getArgument(self::INPUT_KEY_ADDRESS));
        $item->setIsAvailable($input->getArgument(self::INPUT_KEY_IS_AVAILABLE));
        $item->setIsObjectNew(true);
        $item->save();
        $this->logger->debug('Item was created');
        return Cli::RETURN_SUCCESS;
    }

}
