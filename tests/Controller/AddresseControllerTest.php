<?php

namespace App\Tests\Controller;

use App\Entity\Addresse;
use App\Repository\AddresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AddresseControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $addresseRepository;
    private string $path = '/addresse/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->addresseRepository = $this->manager->getRepository(Addresse::class);

        foreach ($this->addresseRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Addresse index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'addresse[titre]' => 'Testing',
            'addresse[Ville]' => 'Testing',
            'addresse[CodePostal]' => 'Testing',
            'addresse[Pays]' => 'Testing',
            'addresse[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->addresseRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Addresse();
        $fixture->setTitre('My Title');
        $fixture->setVille('My Title');
        $fixture->setCodePostal('My Title');
        $fixture->setPays('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Addresse');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Addresse();
        $fixture->setTitre('Value');
        $fixture->setVille('Value');
        $fixture->setCodePostal('Value');
        $fixture->setPays('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'addresse[titre]' => 'Something New',
            'addresse[Ville]' => 'Something New',
            'addresse[CodePostal]' => 'Something New',
            'addresse[Pays]' => 'Something New',
            'addresse[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/addresse/');

        $fixture = $this->addresseRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getVille());
        self::assertSame('Something New', $fixture[0]->getCodePostal());
        self::assertSame('Something New', $fixture[0]->getPays());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Addresse();
        $fixture->setTitre('Value');
        $fixture->setVille('Value');
        $fixture->setCodePostal('Value');
        $fixture->setPays('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/addresse/');
        self::assertSame(0, $this->addresseRepository->count([]));
    }
}
