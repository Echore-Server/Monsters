<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Closure;
use Echore\Stargazer\ModifiableValue;
use Echore\Stargazer\ModifierApplierTypes;
use pocketmine\math\Vector3;
use pocketmine\utils\Utils;

class Vector3EachModifiers {

	public ModifiableValue $x;

	public ModifiableValue $y;

	public ModifiableValue $z;

	public function __construct() {
		$this->x = new ModifiableValue(0.0);
		$this->y = new ModifiableValue(0.0);
		$this->z = new ModifiableValue(0.0);
	}

	/**
	 * @param Closure(ModifiableValue $modifier): void $func
	 * @return void
	 */
	public function forEach(Closure $func): void {
		Utils::validateCallableSignature(function(ModifiableValue $modifier): void {
		}, $func);
		foreach ([$this->x, $this->y, $this->z] as $modifier) {
			$func($modifier);
		}
	}

	public function apply(Vector3 $v): Vector3 {
		$v->x += $this->x->getFinal(ModifierApplierTypes::default());
		$v->y += $this->y->getFinal(ModifierApplierTypes::default());
		$v->z += $this->z->getFinal(ModifierApplierTypes::default());

		return $v;
	}

	public function revert(Vector3 $v): Vector3 {
		$v->x -= $this->x->getFinal(ModifierApplierTypes::default());
		$v->y -= $this->y->getFinal(ModifierApplierTypes::default());
		$v->z -= $this->z->getFinal(ModifierApplierTypes::default());

		return $v;
	}
}
