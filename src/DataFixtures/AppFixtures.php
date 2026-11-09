<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Données de test, chargées avec : php bin/console doctrine:fixtures:load
 * Les tests fonctionnels s'appuient sur ces données : ne les modifiez pas (sauf le point 5).
 */
class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // ===== Utilisateurs =====
        $admin = new User();
        $admin->setEmail('admin@test.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@test.fr');
        $user->setPassword($this->hasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // ===== Événements =====
        $jazz = new Event();
        $jazz->setTitle('Soirée jazz');
        $jazz->setDate(new \DateTime('+10 days 20:30'));
        $jazz->setInformation('Le quartet CuRuba vous propose une soirée de jazz antillais au Kiosque du parc municipal.');
        $jazz->setCategory('concert');
        $jazz->setPlaces(80);
        $jazz->setValidated(true);
        $jazz->setAuthor($user);
        $manager->persist($jazz);

        $marathon = new Event();
        $marathon->setTitle('Semi-marathon');
        $marathon->setDate(new \DateTime('+15 days 09:00'));
        $marathon->setInformation('Départ place de la mairie, 21 km à travers la ville.');
        $marathon->setCategory('sport');
        $marathon->setPlaces(300);
        $marathon->setValidated(true);
        $marathon->addParticipant($user);
        $manager->persist($marathon);

        $poterie = new Event();
        $poterie->setTitle('Initiation à la poterie');
        $poterie->setDate(new \DateTime('+20 days 14:00'));
        $poterie->setInformation('Atelier découverte, matériel fourni.');
        $poterie->setCategory('atelier');
        $poterie->setPlaces(1);
        $poterie->setValidated(true);
        $poterie->addParticipant($admin); // 1 place, 1 inscrit : complet
        $manager->persist($poterie);

        $passe = new Event();
        $passe->setTitle('Concert passé');
        $passe->setDate(new \DateTime('-10 days 20:00'));
        $passe->setInformation('Cet événement est passé : il ne doit pas apparaître sur l\'accueil.');
        $passe->setCategory('concert');
        $passe->setPlaces(80);
        $passe->setValidated(true);
        $manager->persist($passe);

        $basket = new Event();
        $basket->setTitle('Tournoi de basket');
        $basket->setDate(new \DateTime('+30 days 14:00'));
        $basket->setInformation('Tournoi 3x3, inscription par équipe.');
        $basket->setCategory('sport');
        $basket->setPlaces(10);
        // non validé : il doit apparaître sur /validate, pas sur l'accueil
        $manager->persist($basket);

        $manager->flush();
    }
}
