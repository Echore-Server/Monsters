<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity;

use Echore\Stargazer\Modifier;
use Lyrica0954\Monsters\entity\source\MonsterMotionEvent;
use Lyrica0954\Monsters\utils\ValueModifier;
use Lyrica0954\Monsters\utils\Vector3EachModifiers;
use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;

class Motioner {

	protected Entity $entity;

	protected Vector3EachModifiers $modifiers;

	public function __construct(Entity $entity) {
		$this->modifiers = new Vector3EachModifiers();
		$this->entity = $entity;
	}

	/**
	 * Get the value of modifiers
	 *
	 * @return Vector3EachModifiers
	 */
	public function getModifiers(): Vector3EachModifiers {
		return $this->modifiers;
	}

	public function customAttack(EntityDamageByEntityEvent $source, Modifier $xz, Modifier $y, float $base = 0.4): void {
		$this->entity->attack($source);

		if (!$source->isCancelled()) {
			$this->knockBack($source->getDamager()->getPosition(), $xz, $y, $base);
		}
	}

	public function knockBack(Vector3 $from, Modifier $xz, Modifier $y, float $base = 0.4): void {
		$motion = $this->getKnockBack($from, $xz, $y, $base);

		$this->set($motion, MonsterMotionEvent::SOURCE_SIMULATED_KNOCKBACK);
	}

	public function getKnockBack(Vector3 $from, Modifier $xz, Modifier $y, float $base = 0.4): Vector3 {
		$motion = $this->simulateKnockBack($from, $base);

		$motion->x = $motion->x * $xz->multiplier + $xz->absolute;
		$motion->z = $motion->z * $xz->multiplier + $xz->absolute;

		$motion->y = $motion->y * $y->multiplier + $y->absolute;

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
		if (mt_rand() / mt_getrandmax() > ($origin->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)?->getValue() ?? 0.0)) {
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
		} else {
			return new Vector3(0, 0, 0);
		}
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
