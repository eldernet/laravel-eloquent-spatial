<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Tests\TestModels\TestPlace;

uses(DatabaseMigrations::class);

it('creates a model record with point', function (): void {
  $point = new Point(180, 0);

  /** @var TestPlace $testPlace */
  $testPlace = TestPlace::factory()->create(['point' => $point]);

  expect($testPlace->point)->toBeInstanceOf(Point::class);
  expect($testPlace->point)->toEqual($point);
});

it('creates a model record with point with SRID', function (): void {
  $point = new Point(180, 0, 4326);

  /** @var TestPlace $testPlace */
  $testPlace = TestPlace::factory()->create(['point' => $point]);

  expect($testPlace->point->srid)->toBe(4326);
});

it('creates a model record that has a column with SRID defined using point with SRID', function (): void {
  $point = new Point(-43.5309, 172.6365, 4326);   // Christchurch, New Zealand

  /** @var TestPlace $testPlace */
  $testPlace = TestPlace::factory()->create(['point_with_srid' => $point]);

  expect($testPlace->point_with_srid)->toEqual($point);
});

it('creates point from JSON', function (): void {
  $point = new Point(180, 0);

  $pointFromJson = Point::fromJson('{"type":"Point","coordinates":[0,180]}');

  expect($pointFromJson)->toEqual($point);
});

it('creates point with SRID from JSON', function (): void {
  $point = new Point(180, 0, 4326);

  $pointFromJson = Point::fromJson('{"type":"Point","coordinates":[0,180]}', 4326);

  expect($pointFromJson)->toEqual($point);
});

it('generates point JSON', function (): void {
  $point = new Point(180, 0);

  $json = $point->toJson();

  $expectedJson = '{"type":"Point","coordinates":[0,180]}';
  expect($json)->toBe($expectedJson);
});

it('throws exception when creating point from invalid JSON', function (): void {
  expect(function (): void {
    Point::fromJson('{"type":"Point","coordinates":[]}');
  })->toThrow(InvalidArgumentException::class);
});

it('creates point from WKT', function (): void {
  $point = new Point(180, 0);

  $pointFromWkt = Point::fromWkt('POINT(0 180)');

  expect($pointFromWkt)->toEqual($point);
});

it('creates point with SRID from WKT', function (): void {
  $point = new Point(180, 0, 4326);

  $pointFromWkt = Point::fromWkt('POINT(0 180)', 4326);

  expect($pointFromWkt)->toEqual($point);
});

it('generates point WKT', function (): void {
  $point = new Point(180, 0);

  $wkt = $point->toWkt();

  $expectedWkt = 'POINT(0 180)';
  expect($wkt)->toBe($expectedWkt);
});

it('creates point from WKB', function (): void {
  $point = new Point(180, 0);

  $pointFromWkb = Point::fromWkb($point->toWkb());

  expect($pointFromWkb)->toEqual($point);
});

it('creates point with SRID from WKB', function (): void {
  $point = new Point(180, 0, 4326);

  $pointFromWkb = Point::fromWkb($point->toWkb());

  expect($pointFromWkb)->toEqual($point);
});
