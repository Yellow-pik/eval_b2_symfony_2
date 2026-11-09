<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class TodoControllerTest extends WebTestCase
{
    public function testListPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.event-card'));
        $this->assertSelectorTextNotContains('body', 'Concert passé');
        $this->assertSelectorTextNotContains('body', 'Tournoi de basket');
    }

    public function testEventDetail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $client->clickLink('Soirée jazz');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Soirée jazz');
    }

    public function testEventNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenement/9999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSportCategory(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/?categorie=sport');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('.event-title', 'Semi-marathon');
        $this->assertSelectorExists('.legend .cat-sport a.active');
    }

    public function testEventWithoutTitle():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/evenement/ajouter');
        
        $repository = static::getContainer()->get(EventRepository::class);
        $countBefore = $repository->count([]);

        $client->submitForm("Enregistrer l'événement", [
            'event[title]' => '',
            'event[date]' => (new \DateTime('+10 days'))->format('Y-m-d\TH:i'),
            'event[information]' => 'Evenement test',
            'event[category]' => 'concert',
            'event[places]' => 10,
            ]);

        $repository = static::getContainer()->get(EventRepository::class);
        $countAfter = $repository->count([]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('form', 'Le titre est obligatoire.');

        $this->assertSame($countBefore, $countAfter);
    }

    public function testEventWithZeroPlaces():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/evenement/ajouter');
        
        $repository = static::getContainer()->get(EventRepository::class);
        $countBefore = $repository->count([]);

        $client->submitForm("Enregistrer l'événement", [
            'event[title]' => 'Événement sans place',
            'event[date]' => (new \DateTime('+10 days'))->format('Y-m-d\TH:i'),
            'event[information]' => 'Evenement test',
            'event[category]' => 'concert',
            'event[places]' => 0,
            ]);

        $repository = static::getContainer()->get(EventRepository::class);
        $countAfter = $repository->count([]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('form', 'Le nombre de places doit être supérieur à 0.');

        $this->assertSame($countBefore, $countAfter);
    }

    public function testRegisterForEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Soirée jazz']);
        $this->assertNotNull($user);
        $this->assertNotNull($event);
        $eventUrl = '/evenement/'.$event->getId();

        $client->loginUser($user);
        $client->request('GET', $eventUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('.places strong', '80');

        $client->submitForm("S'inscrire");
        $this->assertResponseRedirects($eventUrl);
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.flash-success', 'Vous êtes inscrit à cet événement.');
        $this->assertSelectorTextSame('.places strong', '79');

        $crawler = $client->clickLink('Mes inscriptions');
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('.section-head', 'Vous êtes inscrit à 2 événement(s).');
        $this->assertAnySelectorTextContains('.event-title', 'Soirée jazz');
        $this->assertAnySelectorTextContains('.event-title', 'Semi-marathon');
    }

    public function testUnregisterFromMyEvents(): void
    {
        $client = static::createClient();
        $client->request('GET', '/mes-inscriptions');
        $this->assertResponseRedirects('/login');

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $this->assertNotNull($user);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/mes-inscriptions');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('.event-title', 'Semi-marathon');

        $client->submitForm('Se désinscrire');
        $this->assertResponseRedirects('/mes-inscriptions');
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.flash-success', 'Vous êtes désinscrit de cet événement.');
        $this->assertCount(0, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('.section-head', 'Vous êtes inscrit à 0 événement(s).');
        $this->assertSelectorTextContains('.empty', "Vous n'êtes inscrit à aucun événement pour le moment.");
    }
}
