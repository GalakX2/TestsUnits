<?php

namespace App\Tests\Functional;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TacheTest extends WebTestCase
{
   
    public function testPublicAccessDenied(): void
    {
        $client = static::createClient();      
        $client->request('GET', '/taches/new');
        $this->assertResponseRedirects('/login');
    }

    public function testAuthUserCanCreateTask(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UtilisateurRepository::class);

        $testUser = $userRepository->findOneBy([]);

        if (!$testUser) {
            $this->markTestSkipped('Aucun utilisateur trouvé dans la base de test.');
        }

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/taches/new');
        
        $this->assertResponseIsSuccessful();

        $client->submitForm('Créer', [
            'tache[titre]' => 'Tâche créée par le test automatisé'
        ]);

        $this->assertResponseRedirects('/taches');
        
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Tâche créée par le test automatisé');
    }
}