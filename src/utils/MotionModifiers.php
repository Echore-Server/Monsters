<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use Closure;
use Echore\Stargazer\ModifiableValue;
use Echore\Stargazer\ModifierSet;
use pocketmine\math\Vector3;
use pocketmine\utils\ObjectSet;
use pocketmine\utils\Utils;

class MotionModifiers {

	public ModifierSet $xz;

	public ModifierSet $y;

	public function __construct(int $mode) {
		$this->xz = new ModifierSet($mode);
		$this->y = new ModifierSet($mode);
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

	public function apply(Vector3 $v, float $penetration = 0.0): Vector3 {
		$xz = $this->xz->getResult() + ((1.0 - $this->xz->getResult()) * $penetration);
		$y = $this->y->getResult() + ((1.0 - $this->y->getResult()) * $penetration);
		$v->x *= $xz;
		$v->y *= $y;
		$v->z *= $xz;

		return $v;
	}
}
