<?php

namespace App\Tests\Functional;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TacheTest extends WebTestCase
{
    // Test 1 — Accès interdit aux invités
    public function testPublicAccessDenied(): void
    {
        $client = static::createClient();
        
        // On tente d'accéder à la page de création sans être connecté
        $client->request('GET', '/taches/new');

        // On vérifie qu'on est redirigé vers le login
        // (Le statut est généralement 302 pour une redirection temporaire)
        $this->assertResponseRedirects('/login');
    }

    // Test 2 — Utilisateur connecté peut créer une tâche
    public function testAuthUserCanCreateTask(): void
    {
        $client = static::createClient();
        
        // On récupère le conteneur de services pour accéder au Repository
        $userRepository = static::getContainer()->get(UtilisateurRepository::class);

        // On récupère un utilisateur de test (le premier trouvé en base)
        $testUser = $userRepository->findOneBy([]);

        // Sécurité : Si votre base de test est vide, le test échouera ici.
        // Assurez-vous d'avoir au moins un utilisateur via les fixtures ou manuellement.
        if (!$testUser) {
            $this->markTestSkipped('Aucun utilisateur trouvé dans la base de test.');
        }

        // On simule la connexion avec cet utilisateur
        $client->loginUser($testUser);

        // On accède à la page de création
        $crawler = $client->request('GET', '/taches/new');
        
        // On vérifie que la page s'affiche bien (code 200)
        $this->assertResponseIsSuccessful();

        // On remplit le formulaire
        // 'tache' est le nom par défaut du formulaire généré pour l'entité Tache
        // 'Créer' est le label du bouton submit (à mettre dans votre vue plus tard)
        $client->submitForm('Créer', [
            'tache[titre]' => 'Tâche créée par le test automatisé'
        ]);

        // On vérifie la redirection vers la liste des tâches après succès
        $this->assertResponseRedirects('/taches');
        
        // Optionnel : on suit la redirection pour vérifier que la tâche apparaît
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Tâche créée par le test automatisé');
    }
}