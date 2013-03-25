<?php

namespace Leezy\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PauseTubeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('leezy:pheanstalk:pause-tube')
            ->addArgument('tube', InputArgument::REQUIRED, 'The tube to pause')
            ->addArgument('delay', InputArgument::REQUIRED, 'Seconds before jobs may be reserved from this queue.')
            ->addArgument('pheanstalk', InputArgument::OPTIONAL, 'Pheanstalk name.')
            ->setDescription('Temporarily prevent jobs being reserved from the given tube.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $delay = $input->getArgument('delay');
        $pheanstalkName = $input->getArgument('pheanstalk');
        
        $pheanstalkLocator = $this->getContainer()->get('leezy.pheanstalk.pheanstalk_locator');
        $pheanstalk = $pheanstalkLocator->getPheanstalk($pheanstalkName);
        
        if (null === $pheanstalk) {
            $output->writeln('Pheanstalk not found : <error>' . $pheanstalkName . '</error>');
            return;
        }
        
        if (!$pheanstalk->getPheanstalk()->isServiceListening()) {
            $output->writeln('Pheanstalk not connected : <error>' . $pheanstalkName . '</error>');
            return;
        }
        
        try {
            $pheanstalk->pauseTube ($tube, $delay);
            $output->writeln('Tube <info>' . $tube . '</info> have been paused for <info>' . $delay . '</info> seconds.');
        }
        catch (Exception $ex) {
            $output->writeln('<error>Can pause the tube.</error>');
        }
    }
}
