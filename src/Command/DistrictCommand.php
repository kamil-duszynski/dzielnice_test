<?php

namespace App\Command;

use App\Repository\CityRepository;
use App\Service\District\DistrictService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DistrictCommand extends Command
{
    /**
     * @var DistrictService
     */
    private $importService;

    /**
     * @var CityRepository
     */
    private $repository;

    /**
     * @param DistrictService $districtService
     * @param CityRepository  $repository
     */
    public function __construct(DistrictService $districtService, CityRepository $repository)
    {
        $this->importService = $districtService;
        $this->repository    = $repository;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('import:districts')
            ->setDescription('Command to import districts info')
            ->addArgument('city', InputArgument::OPTIONAL, 'Name of city to import')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cityName = $input->getArgument('city');
        $city     = [];

        if (null !== $cityName) {
            $city = $this->repository->findOneBy(
                [
                    'name' => $cityName,
                ]
            );

            $cities = [ $city ];
        } else {
            $cities = $this->repository->findAll();
        }

        $output->writeln(
            [
                '',
                'Rozpoczynam import dzielnic',
                '',
            ]
        );

        if (null !== $cityName && null === $city) {
            $output->writeln(
                [
                    '',
                    '',
                    sprintf(
                        'Nie znaleziono miasta: %s',
                        $cityName
                    ),
                    'Koniec.',
                ]
            );

            return;
        }

        $citiesCount = count($cities);
        $progress    = new ProgressBar($output);

        $progress->start($citiesCount);

        foreach ($cities as $city) {
            $this->importService->import($city);
            $progress->advance();
        }

        $progress->finish();

        $output->writeln(
            [
                '',
                '',
                'Dzielnice zaimportowano poprawnie.',
                'Koniec.',
            ]
        );
    }
}
