<?php

namespace OpenTribes\Core\Test\Silex\Interactor;

use OpenTribes\Core\Context\Player\CreateNewCity as CreateNewCityInteractor;
use OpenTribes\Core\Request\CreateNewCity as CreateNewCityRequest;
use OpenTribes\Core\Response\CreateNewCity as CreateNewCityResponse;
use OpenTribes\Core\Silex\Repository;
use OpenTribes\Core\Silex\Service;
use OpenTribes\Core\Value\Direction;

class CityTest extends \PHPUnit_Framework_TestCase
{

    private $cityRepository;
    private $mapTilesRepository;
    private $tileRepository;
    private $mapRepository;
    /**
     * @var \OpenTribes\Core\Silex\Repository\DBALUser;
     */
    private $userRepository;
    private $locationCalculator;

    public function setUp()
    {
        $env                      = 'test';
        $app                      = require __DIR__ . '/../../../bootstrap.php';
        $this->mapRepository      = $app[Repository::MAP];
        $this->tileRepository     = $app[Repository::TILE];
        $this->cityRepository     = $app[Repository::CITY];
        $this->mapTilesRepository = $app[Repository::MAP_TILES];
        $this->userRepository     = $app[Repository::USER];
        $this->locationCalculator = $app[Service::LOCATION_CALCULATOR];
        $this->createDummyUser();
        $this->createDummyMap($app['map.options']);
    }

    private function createDummyUser()
    {
        $user = $this->userRepository->create(1, 'Test', '123456', 'test@test.de');
        $this->userRepository->add($user);
    }

    private function createDummyMap($mapOptions)
    {
        $mapRepository     = $this->mapRepository;
        $tileRepository    = $this->tileRepository;
        $mapTileRepository = $this->mapTilesRepository;
        $mapId             = $mapRepository->getUniqueId();
        $map               = $mapRepository->create($mapId, 'Dummy');
        $map->setWidth($mapOptions['width']);
        $map->setHeight($mapOptions['height']);
        $mapRepository->add($map);


        $tileId = $tileRepository->getUniqueId();
        $tile   = $tileRepository->create($tileId, 'gras', true);
        $tile->setWidth($mapOptions['tileWidth']);
        $tile->setHeight($mapOptions['tileHeight']);
        $tile->setDefault(true);
        $tileRepository->add($tile);
        $tiles   = array('forrest', 'hill', 'sea');
        $tileIds = array();
        foreach ($tiles as $tileName) {
            $tileId = $tileRepository->getUniqueId();
            $tile   = $tileRepository->create($tileId, $tileName, false);
            $tile->setWidth($mapOptions['tileWidth']);
            $tile->setHeight($mapOptions['tileHeight']);
            $tileRepository->add($tile);
            $tileIds[$tileId] = $tileId;
        }

        for ($y = 0; $y <= $mapOptions['height']; $y++) {
            for ($x = 0; $x <= $mapOptions['width']; $x++) {
                $rand = rand(0, 100);
                if ($rand > 80) {
                    $randomTileId = $tileIds[array_rand($tileIds)];
                    $tile         = $tileRepository->findById($randomTileId);
                    $map->addTile($tile, $y, $x);
                }
            }
        }

        $mapTileRepository->add($map);
    }

    public function testCreateRandomUniqueCities()
    {


        $interactor = new CreateNewCityInteractor($this->cityRepository, $this->mapTilesRepository, $this->userRepository, $this->locationCalculator);
        $response   = new CreateNewCityResponse;
        $locations  = array();
        for ($i = 0; $i < 20; $i++) {
            $request          = new CreateNewCityRequest('Test', Direction::ANY, 'TestCity ' . $i);
            $response->failed = $interactor->process($request, $response);
            $y                = $response->city->y;
            $x                = $response->city->x;
            $key              = sprintf('%d-%d', $y, $x);
            $this->assertFalse(isset($locations[$key]), sprintf('City at location Y:%d X:%d already exists', $y, $x));
            $locations[$key] = array(
                'x' => $x,
                'y' => $y,
                'i' => $i
            );

            $this->assertNotNull($response->city);
        }

    }

}
