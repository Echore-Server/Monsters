<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Closure;
use Echore\Stargazer\ModifiableValue;
use Echore\Stargazer\ModifierApplier;
use Echore\Stargazer\ModifierApplierTypes;
use pocketmine\math\Vector3;
use pocketmine\utils\ObjectSet;
use pocketmine\utils\Utils;

class MotionModifiers {

	public ObjectSet $xz;

	public ObjectSet $y;

	protected ModifierApplier $applier;

	public function __construct() {
		$this->xz = new ObjectSet();
		$this->y = new ObjectSet();
		$this->applier = ModifierApplierTypes::default();
	}

	/**
	 * @param Closure(ModifiableValue $modifier): void $func
	 * @return void
	 */
	public function forEach(Closure $func): void {
		Utils::validateCallableSignature(function(ObjectSet $modifiers): void {
		}, $func);
		foreach ([$this->xz, $this->y] as $modifier) {
			$func($modifier);
		}
	}

	public function apply(Vector3 $v): Vector3 {
		$v->x = $this->applier->apply($v->x, $this->xz->toArray());
		$v->y = $this->applier->apply($v->y, $this->y->toArray());
		$v->z = $this->applier->apply($v->z, $this->xz->toArray());

		return $v;
	}
}
