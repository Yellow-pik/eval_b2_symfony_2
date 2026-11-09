<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\Event;
use App\Entity\User;

class EventTest extends TestCase
{
    #[DataProvider('remainingPlacesProvider')]
    public function testRemainingPlaces(
        int $places, 
        int $participants, 
        int $expectedRemainingPlaces
    ): void {

    $event = new Event();
    $event->setPlaces($places);

    for ($i = 0; $i < $participants; $i++) {
        $event->addParticipant(new User());
    }
    
    $this->assertSame($expectedRemainingPlaces, $event->getRemainingPlaces());
    }
    #[DataProvider('categoryLabelProvider')]
    public function testCategoryLabel(
        string $category, 
        string $expectedLabel
    ): void{
        $event = new Event();
        $event->setCategory($category);

        $this->assertSame($expectedLabel, $event->getCategoryLabel());
    }

    public static function remainingPlacesProvider(): array
    {
    return [
        'aucun inscrit' => [10, 0, 10],
        'quelques inscrits' => [10, 2, 8],
        'événement complet' => [10, 10, 0],
    ];
    }
    public static function categoryLabelProvider(): array
    {
    return [
        'conférence' => ['conference', 'Conférence'],
        'concert' => ['concert', 'Concert'],
        'atelier' => ['atelier', 'Atelier'],
        'sport' => ['sport', 'Sport'],
    ];
    
}
}
