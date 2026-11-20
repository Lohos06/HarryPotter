<?php

namespace App\Serializer;

use App\Entity\House;
use App\Entity\Species;
use App\Entity\Title;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\Service\ResetInterface;

// Priorité supérieure à celle d'API Platform, sinon une chaîne est interprétée comme une IRI
#[AutoconfigureTag('serializer.normalizer', ['priority' => 100])]
final class NameDenormalizer implements DenormalizerInterface, ResetInterface
{
    private const TYPES = [House::class, Species::class, Title::class];

    /** @var array<string, object> évite de créer deux fois la même entité dans une requête */
    private array $cache = [];

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): object
    {
        $name = mb_strtolower(trim($this->extractName($data)));

        if ('' === $name) {
            throw new UnexpectedValueException('Le nom ne peut pas être vide.');
        }

        $key = $type . '|' . $name;

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $entity = $this->em->getRepository($type)->findOneBy(['name' => $name]);

        if (null === $entity) {
            $entity = (new $type())->setName($name);
            $this->em->persist($entity);
        }

        return $this->cache[$key] = $entity;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        if (!in_array($type, self::TYPES, true)) {
            return false;
        }

        // "gryffindor" ou {"name": "gryffindor"} ; une IRI (/api/houses/1) reste gérée par API Platform
        return (is_string($data) && !str_starts_with($data, '/'))
            || (is_array($data) && is_string($data['name'] ?? null));
    }

    public function getSupportedTypes(?string $format): array
    {
        return array_fill_keys(self::TYPES, false);
    }

    public function reset(): void
    {
        $this->cache = [];
    }

    private function extractName(mixed $data): string
    {
        return is_array($data) ? $data['name'] : $data;
    }
}