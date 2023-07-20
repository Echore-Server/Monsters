<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\shape;

use pocketmine\math\Vector3;

class TriangleShape implements IShape {

	public Vector3 $a;

	public Vector3 $b;

	public Vector3 $c;

	public function __construct(Vector3 $a, Vector3 $b, Vector3 $c) {
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}

	public function ba(): float {
		return $this->ab();
	}

	public function ab(): float {
		return $this->a->distance($this->b);
	}

	public function cb(): float {
		return $this->bc();
	}

	public function bc(): float {
		return $this->b->distance($this->c);
	}

	public function ca(): float {
		return $this->ac();
	}

	public function ac(): float {
		return $this->a->distance($this->c);
	}

	public function getSumOfInteriorAngles(): float {
		return 180;
	}
}
