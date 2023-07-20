<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;

class EntityDamageEffectEvent extends EntityEvent implements Cancellable {
	use CancellableTrait;

	public const CAUSE_CONTACT = 0;

	public const CAUSE_ENTITY_ATTACK = 1;

	public const CAUSE_PROJECTILE = 2;

	public const CAUSE_SUFFOCATION = 3;

	public const CAUSE_FALL = 4;

	public const CAUSE_FIRE = 5;

	public const CAUSE_FIRE_TICK = 6;

	public const CAUSE_LAVA = 7;

	public const CAUSE_DROWNING = 8;

	public const CAUSE_BLOCK_EXPLOSION = 9;

	public const CAUSE_ENTITY_EXPLOSION = 10;

	public const CAUSE_VOID = 11;

	public const CAUSE_SUICIDE = 12;

	public const CAUSE_MAGIC = 13;

	public const CAUSE_CUSTOM = 14;

	public const CAUSE_STARVATION = 15;

	protected int $cause;

	public function __construct(
		Entity $entity,
		int    $cause,
	) {
		$this->entity = $entity;
		$this->cause = $cause;
	}

	public function getCause(): int {
		return $this->cause;
	}
}
