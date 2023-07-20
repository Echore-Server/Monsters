<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use Closure;
use pocketmine\entity\Entity;

class EntitySearchOption {

	protected array $exludeSet;

	/**
	 * @param bool $includeDead
	 * @param string $entityType
	 * @param int|null $max
	 * @param int[] $exclude entity runtime id array
	 */
	public function __construct(
		public bool     $includeDead,
		public string   $entityType,
		public ?int     $max,
		protected array $exclude,
		public ?Closure $filter
	) {
		$this->setExclude($exclude);
	}

	public static function includeDead(bool $v): self {
		$i = self::default();
		$i->includeDead = $v;

		return $i;
	}

	public static function default(): self {
		return new self(false, Entity::class, null, [], null);
	}

	public static function entityType(string $v): self {
		$i = self::default();
		$i->entityType = $v;

		return $i;
	}

	public static function filter(Closure $filter): self {
		$i = self::default();
		$i->filter = $filter;

		return $i;
	}

	public static function max(int $v): self {
		$i = self::default();

		$i->max = $v;

		return $i;
	}

	/**
	 * @param int[] $v
	 *
	 * @return self
	 */
	public static function exclude(array $v): self {
		$i = self::default();
		$i->setExclude($v);

		return $i;
	}

	public function setExclude(array $exclude): void {
		$this->exclude = $exclude;
		$this->exludeSet = array_flip($exclude);
	}

	public function getExclude(): array {
		return $this->exclude;
	}

	public function isExcluded(int $runtimeId): bool {
		return isset($this->exludeSet[$runtimeId]); // in_array is weight
	}
}
