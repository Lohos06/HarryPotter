<?php

namespace App\Command;

use App\Entity\Character;
use App\Entity\House;
use App\Entity\Species;
use App\Entity\Title;
use App\Repository\CharacterRepository;
use App\Repository\HouseRepository;
use App\Repository\SpeciesRepository;
use App\Repository\TitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-characters',
    description: 'Importe 50 personnages d\'une maison depuis PotterDB',
)]
class ImportCharactersCommand
{
    // import : ce qu'on a déjà créé pendant l'import
    // (avant le flush, findOneBy ne voit pas encore les nouvelles lignes)
    private array $housesVues = [];
    private array $speciesVues = [];
    private array $titlesVus = [];

    // les outils pour la commande
    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $em,
        private CharacterRepository $characters,
        private HouseRepository $houses,
        private SpeciesRepository $species,
        private TitleRepository $titles,
    ) {}

    // lancement la commande
    public function __invoke(
        #[Argument('La maison : gryffindor, slytherin, hufflepuff ou ravenclaw')] string $maison,
        SymfonyStyle $io,
    ): int {
        // E - EXTRAIRE : 50 personnages de la maison demandée
        // mais PotterDB attend le nom avec une majuscule : "Gryffindor"
        $reponse = $this->client->request('GET', 'https://api.potterdb.com/v1/characters', [
            'query' => [
                'filter[house_eq]' => ucfirst(strtolower($maison)), //si le user met la maison avec une minuscule au debut bah il recojnait quand meme
                'page[size]' => 50,
            ],
        ]);
        $donnees = $reponse->toArray();

        $importes = 0;

        foreach ($donnees['data'] ?? [] as $item) {
            $a = $item['attributes'] ?? [];

            // T - règle 1  : sans nom, le personnage est inutilisable
            if (empty($a['name'])) {
                continue;
            }

            // T - règle 2  : clé naturelle sourceId déjà en base = déjà importé
            if ($this->characters->findOneBy(['sourceId' => $item['id']])) {
                continue;
            }

            // T - mapping : on range chaque info de PotterDB dans le personnage
            // chaque texte est coupé à sa longueur max pour que ça rentre ans la bdd
            $character = (new Character())
                ->setSourceId($item['id'])
                ->setName(mb_substr(trim($a['name']), 0, 255))
                ->setGender($this->texte($a['gender'] ?? null, 20, true))
                ->setBloodStatus($this->texte($a['blood_status'] ?? null, 50, true))
                ->setBorn($this->texte($a['born'] ?? null, 255))
                ->setDied($this->texte($a['died'] ?? null, 255))
                ->setHeight($this->texte($a['height'] ?? null, 50))
                ->setWeight($this->texte($a['weight'] ?? null, 50))
                ->setNationality($this->texte($a['nationality'] ?? null, 100))
                ->setImage($this->lien($a['image'] ?? null))
                ->setWikiLink($this->lien($a['wiki'] ?? null));

            // T - règle 3 : la maison est retrouvée sinon l'import la crée
            if (!empty($a['house'])) {
                $character->setHouse($this->trouverOuCreerHouse($a['house']));
            }

            // T - règle 4  : l'espèce est retrouvée ou créée(import)
            if (!empty($a['species'])) {
                $character->setSpecies($this->trouverOuCreerSpecies($a['species']));
            }

            // T - règle 5  : les 3 premiers titres, retrouvés ou créés (import)
            foreach (array_slice($a['titles'] ?? [], 0, 3) as $titre) {
                if (trim((string) $titre) !== '') {
                    $character->addTitle($this->trouverOuCreerTitle($titre));
                }
            }

            // L - CHARGER : persist met le personnage en attente (pas encore en base)...
            $this->em->persist($character);
            $importes++;
        }

        // L - ...et on écrit tout d'un coup, à la fin
        $this->em->flush();

        $io->success(sprintf('%d personnages importés.', $importes));

        return Command::SUCCESS;
    }

    // Retrouve la maison par son nom (en minuscules car par defaut dans l'api y'a une majuscule), sinon la crée
    private function trouverOuCreerHouse(string $nom): House
    {
        $nom = mb_substr(mb_strtolower(trim($nom)), 0, 50);

        if (isset($this->housesVues[$nom])) {
            return $this->housesVues[$nom];
        }

        $house = $this->houses->findOneBy(['name' => $nom]) ?? (new House())->setName($nom);
        $this->em->persist($house);

        return $this->housesVues[$nom] = $house;
    }

    // Retrouve l'espèce par son nom (en minuscules), sinon la crée
    private function trouverOuCreerSpecies(string $nom): Species
    {
        $nom = mb_substr(mb_strtolower(trim($nom)), 0, 100);

        if (isset($this->speciesVues[$nom])) {
            return $this->speciesVues[$nom];
        }

        $species = $this->species->findOneBy(['name' => $nom]) ?? (new Species())->setName($nom);
        $this->em->persist($species);

        return $this->speciesVues[$nom] = $species;
    }

    // Retrouve le titre par son nom (en minuscules), sinon le crée
    private function trouverOuCreerTitle(string $nom): Title
    {
        $nom = mb_substr(mb_strtolower(trim($nom)), 0, 100);

        if (isset($this->titlesVus[$nom])) {
            return $this->titlesVus[$nom];
        }

        $title = $this->titles->findOneBy(['name' => $nom]) ?? (new Title())->setName($nom);
        $this->em->persist($title);

        return $this->titlesVus[$nom] = $title;
    }

    // Nettoie un texte : trim, minuscules si demandé,enleve les espaces , coupé à $max, null si vide
    private function texte(?string $valeur, int $max, bool $minuscules = false): ?string
    {
        if ($valeur === null || trim($valeur) === '') {
            return null;
        }
        $valeur = trim($valeur);
        if ($minuscules) {
            $valeur = mb_strtolower($valeur);
        }

        return mb_substr($valeur, 0, $max);
    }

    //Pour les liens images et wiki
    // Un lien coupé ne marche plus : s'il est trop long, on ne le garde pas
    private function lien(?string $url): ?string
    {
        if ($url === null || trim($url) === '' || mb_strlen($url) > 255) {
            return null;
        }

        return trim($url);
    }
}