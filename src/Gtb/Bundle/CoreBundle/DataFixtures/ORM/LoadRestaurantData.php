<?php

namespace ERunner\Bundle\FinBackEndBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadRestaurantData.
 *
 * @author Alejandro Bustamante <alejandro.bustamante.serrano@gmail.com>
 */
class LoadRestaurantData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($x = 0 ; $x < 10 ; ++$x) {

            $file = new File();
            $file->setPath('bannerFile.png');
            $file->setOriginalName('bannerFile.png');

            $banner = new Banner();
            $banner->setTitle(sprintf('Banner %s', $x));
            $banner->setLink('http://www.erunner.eu');
            $banner->setFile($file);

            if ($x % 2 == 0) {
                $banner->setCategory($this->getReference(sprintf('Category[%s]', rand(1, 4))));
            } else {
                $banner->setCategory($this->getReference('Category[general]'));
            }

            $this->setReference(sprintf('Banner[%s]', $x), $banner);
            $manager->persist($banner);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 12;
    }
}
