<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use Lyrica0954\Monsters\timings\MonstersTimings;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class MonsterUtil {

	public static function is(Entity $entity, bool $allowPlayer = true): bool {
		return !is_null(self::cast($entity, $allowPlayer));
	}

	public static function cast(Entity $entity, bool $allowPlayer = true): ?MonsterBase {
		return ($entity instanceof Player && $allowPlayer) ? MonsterPlayer::get($entity) : ($entity instanceof MonsterBase ? $entity : null);
	}

	public static function get(MonsterBase|Player $entity): MonsterBase {
		return ($entity instanceof Player) ? MonsterPlayer::get($entity) : $entity;
	}

	/**
	 * @param iterable<mixed, (Entity|RayTraceEntityResult)> $ite
	 *
	 * @return MonsterBase[]
	 */
	public static function filterMonster(iterable $ite, bool $player = false): array {
		$monsters = [];

		MonstersTimings::$filtering->startTiming();
		foreach ($ite as $entity) {
			if ($entity instanceof RayTraceEntityResult) {
				$entity = $entity->getEntity();
			}

			$e = self::cast($entity, $player);
			if ($e instanceof MonsterBase) {
				$monsters[] = $e;
			}
		}

		MonstersTimings::$filtering->stopTiming();

		return $monsters;
	}

	/**
	 * @param iterable<mixed, RayTraceEntityResult> $ite
	 * @param bool $player
	 *
	 * @return (array{RayTraceEntityResult, MonsterBase})[]
	 */
	public static function filterMonsterRayTrace(iterable $ite, bool $player = false): array {
		$results = [];

		MonstersTimings::$filtering->startTiming();
		foreach ($ite as $result) {
			$entity = $result->getEntity();

			$e = self::cast($entity, $player);

			if ($e instanceof MonsterBase) {
				$results[] = [$result, $e];
			}
		}
		MonstersTimings::$filtering->stopTiming();

		return $results;
	}
}
