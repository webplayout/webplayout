<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use App\Mlt\PlayoutClient;

class ScheduleWorkerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webplayout:schedule-worker')
            ->setDescription('webplayout schedule worker command')
            // ->addArgument(
            //     'unit',
            //     InputArgument::REQUIRED,
            //     'unit name'
            // )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $date = new \DateTime;
        //$date = $date->format('Y-m-d');

        $schedules = $em->getRepository('App\Entity\Schedule')
           ->findBy(array('date' => $date ));

        if (!sizeof($schedules)) {
            return;
        }

        //$unit = $input->getArgument('unit');
        $unit = 0;

        $melted_host = $this->getContainer()->getParameter('melted_host');
        $melted_port = $this->getContainer()->getParameter('melted_port');

        $playoutConfig = array(
            'host' => $melted_host,
            'port' => $melted_port,
            'mode' => 1
        );

        $playout = new PlayoutClient($playoutConfig);

        $playout->Mvcp->connect();
        $playout->Mvcp->setUnit($unit);

        $playout->clear($unit);

        foreach ($schedules as $schedule) {
            $file = $schedule->getFile()->getFile();
            $output->writeln("Append $file");

            $playout->append($file/*, null, null, $unit*/);
        $playout->append('.68c39027-c7fc-11e9-a22d-0242ac1b0002.xml'/*, null, null, $unit*/);

        }

        //$playout->load($file, null, null, $unit);

        // $playout->set_in($in,1);
        $playout->play($unit);

        $playout->Mvcp->disconnect();
    }
}
