<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Generator;
use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use Lyrica0954\SmartEntity\entity\walking\FightingEntity;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
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
	 * @param Entity $entity
	 *
	 * @return Living|null
	 */
	public static function castFighting(Entity $entity): ?Living {
		$casted = self::cast($entity, false);

		if ($casted->getEntity() instanceof FightingEntity) {
			return $casted->getEntity();
		}

		return null;
	}


	/**
	 * @param Generator|Entity[]|RayTraceEntityResult[] $ite
	 *
	 * @return Generator<mixed, mixed, MonsterBase, mixed>
	 */
	public static function filterMonster(array|Generator $ite, bool $player = false): Generator {
		foreach ($ite as $entity) {
			if ($entity instanceof RayTraceEntityResult) {
				$entity = $entity->getEntity();
			}

			$e = self::cast($entity, $player);
			if ($e instanceof MonsterBase) {
				yield $e;
			}
		}
	}

	/**
	 * @param Generator|RayTraceEntityResult[] $ite
	 * @param bool $player
	 *
	 * @return Generator<mixed, mixed, array{0: RayTraceEntityResult, 1: MonsterBase}, mixed>
	 */
	public static function filterMonsterRayTrace(Generator $ite, bool $player = false): Generator {
		foreach ($ite as $result) {
			$entity = $result->getEntity();

			$e = self::cast($entity, $player);

			if ($e instanceof MonsterBase) {
				yield [
					0 => $result,
					1 => $e
				];
			}
		}
	}

	/**
	 * @param Generator|Entity[] $ite
	 *
	 * @return Generator<mixed, mixed, (MonsterBase&FightingEntity), mixed>
	 */
	public static function filterFightMonster(Generator $ite, bool $includePlayer = false): Generator {
		foreach ($ite as $entity) {
			$e = self::cast($entity, false);

			if (($e instanceof MonsterBase && $e instanceof FightingEntity) || ($e instanceof MonsterPlayer && $includePlayer)) {
				yield $e;
			}
		}
	}
}
