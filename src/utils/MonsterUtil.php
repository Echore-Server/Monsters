<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Generator;
use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use Traversable;

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
	 * @param Traversable<mixed, (Entity|RayTraceEntityResult)> $ite
	 *
	 * @return Generator<mixed, mixed, MonsterBase, mixed>
	 */
	public static function filterMonster(Traversable $ite, bool $player = false): Generator {
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
	 * @param Traversable<mixed, RayTraceEntityResult> $ite
	 * @param bool $player
	 *
	 * @return Generator<mixed, RayTraceEntityResult, MonsterBase, mixed>
	 */
	public static function filterMonsterRayTrace(Traversable $ite, bool $player = false): Generator {
		foreach ($ite as $result) {
			$entity = $result->getEntity();

			$e = self::cast($entity, $player);

			if ($e instanceof MonsterBase) {
				yield $result => $e;
			}
		}
	}

	/**
	 * @param Traversable<mixed, Entity> $ite
	 *
	 * @return Generator<mixed, mixed, (MonsterBase&FightingEntity), mixed>
	 */
	public static function filterFightMonster(Traversable $ite, bool $includePlayer = false): Generator {
		foreach ($ite as $entity) {
			$e = self::cast($entity, false);

			if (($e instanceof MonsterBase && $e instanceof FightingEntity) || ($e instanceof MonsterPlayer && $includePlayer)) {
				yield $e;
			}
		}
	}
}
