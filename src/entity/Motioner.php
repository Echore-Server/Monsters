<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity;

use Echore\Stargazer\ModifierSet;
use Lyrica0954\Monsters\entity\source\MonsterMotionEvent;
use Lyrica0954\Monsters\utils\MotionModifiers;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;

class Motioner {

	protected Entity $entity;

	protected MotionModifiers $modifiers;

	public function __construct(Entity $entity) {
		$this->modifiers = new MotionModifiers(ModifierSet::MODE_MULTIPLICATION);
		$this->entity = $entity;
		$this->entity->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)->setValue(1.0);
	}

	/**
	 * Get the value of modifiers
	 *
	 * @return MotionModifiers
	 */
	public function getModifiers(): MotionModifiers {
		return $this->modifiers;
	}

	public function customAttack(EntityDamageByEntityEvent $source, float $xz = null, float $y = null, ?float $onGroundModifier = null): void {
		$xz ??= 1.0;
		$y ??= 1.0;
		$onGroundModifier ??= 1.0;
		$this->entity->attack($source);

		$kb = $source->getKnockBack();

		if (!$source->isCancelled()) {
			if ($this->entity->isOnGround()) {
				$xz = $xz * $onGroundModifier;
				$y = $y * $onGroundModifier;
			}
			$damager = $source->getDamager();

			if ($source instanceof EntityDamageByChildEntityEvent) {
				$damager = $source->getChild() ?? $source->getDamager();
			}

			if ($damager !== null && $damager->isAlive() && !$damager->isClosed() && !$damager->isFlaggedForDespawn()) {
				$this->knockBack($damager->getPosition(), $xz, $y, $kb);
			}
		}
	}

	public function getKnockBack(Vector3 $from, float $xz, float $y, float $base = 0.4): Vector3 {
		$motion = $this->simulateKnockBack($from, $base);

		$motion->x = $motion->x * $xz;
		$motion->z = $motion->z * $xz;

		$motion->y = $motion->y * $y;

		return $motion;
	}

	public function simulateKnockBack(Vector3 $from, float $base = 0.4): Vector3 {
		$origin = $this->entity;

		$x = $origin->getPosition()->x - $from->x;
		$z = $origin->getPosition()->z - $from->z;

		$f = sqrt($x * $x + $z * $z);
		if ($f <= 0) {
			return new Vector3(0, 0, 0);
		}
		$f = 1 / $f;
		$motion = clone $origin->getMotion();
		$motion->x /= 2;
		$motion->y /= 2;
		$motion->z /= 2;
		$motion->x += $x * $f * $base;
		$motion->y += $base;
		$motion->z += $z * $f * $base;
		if ($motion->y > $base) {
			$motion->y = $base;
		}

		return $motion;
	}

	public function knockBack(Vector3 $from, float $xz, float $y, float $base = 0.4): void {
		if ($base <= 0.0) {
			return;
		}
		$motion = $this->getKnockBack($from, $xz, $y, $base);

		$this->set($motion, MonsterMotionEvent::SOURCE_SIMULATED_KNOCKBACK);
	}

	public function set(Vector3 $motion, int $source = MonsterMotionEvent::SOURCE_UNKNOWN): void {
		$v = $this->modifiers->apply(clone $motion);

		$ev = new MonsterMotionEvent($this->entity, $v, $source);
		$ev->call();

		$this->entity->setMotion($v);
	}

	public function add(float $x, float $y, float $z): void {
		$v = new Vector3($x, $y, $z);
		$this->modifiers->apply($v);
		$this->entity->addMotion($v->x, $v->y, $v->z);
	}
}
