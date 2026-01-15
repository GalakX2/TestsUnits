<?php 

namespace App\Tests\Functional;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
final class SecurityTest extends WebTestCase
{
 
   public function testRedirection()
   {
    $client = static::createClient();
    $client->request('GET','/tache');
    $this->assertResponseRedirects('/login');
   }
   public function testInscriptionConnexion(){
    $client = static::createClient();
    $crawler = $client->request('GET','/register');
    $email = 'testuser+'.uniqid('',true)."@test.local";
    $from = $crawler->selectButton('Register')->form([
        'registration_form[email]'=> $email,
        'registration_form[plainPassword]'=> 'pass1234',
        'registration_form[agreeTerms]'=> 1,
    ]);
    $client->submit($from);
    $this->assertResponseRedirects();
    $client->followRedirect();
    $client->request('GET', '/tache');
    $this->assertResponseIsSuccessful();
   }

}