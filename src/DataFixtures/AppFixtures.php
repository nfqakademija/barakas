<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Academy;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $universities = [
            'Vilniaus Universitetas',
            'Vilniaus Gedimino technikos universitetas',
            'Vilniaus dailės akademija',
            'Generolo Jono Žemaičio Lietuvos karo akademija',
            'Lietuvos muzikos ir teatro akademija',
            'Lietuvos sveikatos mokslų universitetas',
            'Kauno technologijos universitetas',
            'Lietuvos sporto universitetas',
            'Mykolo Romerio universitetas',
            'Vytauto Didžiojo universitetas',
            'Šiaulių universitetas',
            'Klaipėdos universitetas',
            'ISM Vadybos ir ekonomikos universitetas',
            'LCC tarptautinis universitetas',
            'Kazimiero Simonavičiaus universitetas',
            'Telšių Vyskupo Vincento Borisevičiaus kunigų seminarija',
            'Europos Humanitarinis Universitetas',
            'Vilniaus Šv. Juozapo kunigų seminarija'

        ];

        $colleges = [
            'Alytaus kolegija',
            'Kauno kolegija',
            'Kauno miškų ir aplinkos inžinerijos kolegija',
            'Kauno technikos kolegija',
            'Klaipėdos valstybinė kolegija',
            'Lietuvos aukštoji jūreivystės mokykla',
            'Marijampolės kolegija',
            'Panevėžio kolegija',
            'Šiaulių valstybinė kolegija',
            'Utenos kolegija',
            'Vilniaus kolegija',
            'Vilniaus technologijų ir dizaino kolegija',
            'V. A. Graičiūno aukštoji vadybos mokykla',
            'Socialinių mokslų kolegija',
            'Klaipėdos verslo kolegija',
            'Kolpingo kolegija',
            'Šiaurės Lietuvos kolegija',
            'Šv. Ignaco Lojolos kolegija',
            'Tarptautinė teisės ir verslo aukštoji mokykla',
            'Vakarų Lietuvos verslo kolegija',
            'Vilniaus verslo kolegija',
            'Vilniaus dizaino kolegija',
            'Vilniaus kooperacijos kolegija',

        ];

        foreach ($colleges as $value) {
            $college = new Academy();
            $college->setTitle($value);
            $college->setAcademyType(1);
            $manager->persist($college);
        }

        foreach ($universities as $value) {
            $university = new Academy();
            $university->setTitle($value);
            $university->setAcademyType(0);
            $manager->persist($university);
        }

        $manager->flush();
    }
}
