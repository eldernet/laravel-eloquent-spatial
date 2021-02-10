<?php

declare(strict_types=1);

namespace MatanYadaev\EloquentSpatial\Objects;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Query\Expression;
use InvalidArgumentException;
use JsonSerializable;
use MatanYadaev\EloquentSpatial\Factory;
use MatanYadaev\EloquentSpatial\GeometryCast;

abstract class Geometry implements Castable, Arrayable, Jsonable, JsonSerializable
{
    abstract public function toWkt(): Expression;

    public function toJson($options = 0): string
    {
        return json_encode($this, $options);
    }

    /**
     * @param string $wkb
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public static function fromWkb(string $wkb): static
    {
        $geometry = Factory::parse($wkb);

        if (! ($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometry::class)
            );
        }

        return $geometry;
    }

    /**
     * @param string $geoJson
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public static function fromJson(string $geoJson): static
    {
        $geometry = Factory::parse($geoJson);

        if (! ($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometry::class)
            );
        }

        return $geometry;
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array{type: string, coordinates: array<mixed>}
     */
    public function toArray(): array
    {
        return [
            'type' => class_basename(static::class),
            'coordinates' => $this->getCoordinates(),
        ];
    }

    /**
     * @return array{
     *   type: string, properties: array<mixed>, geometry: array{type: string, coordinates: array<mixed>}
     * }
     */
    public function toFeature(): array
    {
        return [
            'type' => 'Feature',
            'properties' => [],
            'geometry' => $this->toArray(),
        ];
    }

    /**
     * @return array<mixed>
     */
    abstract public function getCoordinates(): array;

    /**
     * @param array<string> $arguments
     *
     * @return CastsAttributes
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new GeometryCast(static::class);
    }
}
