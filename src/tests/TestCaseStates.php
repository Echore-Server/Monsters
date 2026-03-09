<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\tests;

use Echore\PMRuntimeUnit\TestCaseParticipant;
use Echore\PMRuntimeUnit\TestCaseWithParticipant;
use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\player\MonsterPlayer;
use Lyrica0954\Monsters\entity\state\DurationState;
use Lyrica0954\Monsters\entity\state\State;

class TestCaseStates extends TestCaseWithParticipant {

	protected MonsterBase $monster;

	public function testApply(): void {
		$this->monster->getStates()->apply(
			$state = new class($this->monster) extends State {

				public function onApply(): void {

				}

				public function onRemove(): void {

				}
			}
		);

		$this->assertTrue($this->monster->getStates()->hasEqual($state), "has state");
	}

	public function testRemoval(): void {
		$this->duration(2);
		$this->monster->getStates()->apply(
			$state = new class($this->monster, 1) extends DurationState {

				public function onApply(): void {

				}

				public function onRemove(): void {

				}
			}
		);

		$this->on(2, function() use ($state): void {
			$this->assertFalse($this->monster->getStates()->hasEqual($state));
		});
	}

	public function testSetRemainTick(): void {
		$this->duration(4);
		$this->monster->getStates()->apply(
			$state = new class($this->monster, 3) extends DurationState {

				public function onApply(): void {

				}

				public function onRemove(): void {

				}
			}
		);

		$this->assertTrue($this->monster->getStates()->hasEqual($state));

		$this->on(1, function() use ($state): void {
			$state->setRemainTick(2);
		});

		$this->on(3, function() use ($state): void {
			$this->assertTrue($this->monster->getStates()->hasEqual($state));
		});

		$this->on(4, function() use ($state): void {
			$this->assertFalse($this->monster->getStates()->hasEqual($state));
		});
	}

	public function testOverrideSetRemainTickInOnce(): void {
		$this->duration(5);
		$this->monster->getStates()->apply(
			$state = new class($this->monster, 2) extends DurationState {

				public function onApply(): void {

				}

				public function onRemove(): void {

				}
			}
		);

		$this->assertTrue($this->monster->getStates()->hasEqual($state));

		$this->on(1, function() use ($state): void {
			$this->assertTrue($state->getRemainTick() === 1);
			for ($i = 0; $i < 10; $i++) {
				$state->setRemainTick(3);
			}
		});

		$this->on(2, function() use ($state): void {
			$this->assertTrue($state->getRemainTick() === 2);
		});

		$this->on(3, function() use ($state): void {
			$this->assertTrue($this->monster->getStates()->hasEqual($state));
		});

		$this->on(5, function() use ($state): void {
			$this->assertFalse($this->monster->getStates()->hasEqual($state));
		});
	}

	public function testOverrideSetRemainTick(): void {
		$this->duration(4);
		$this->monster->getStates()->apply(
			$state = new class($this->monster, 6) extends DurationState {

				public function onApply(): void {

				}

				public function onRemove(): void {

				}
			}
		);

		$this->assertTrue($this->monster->getStates()->hasEqual($state));

		$this->on(1, function() use ($state): void {
			$this->assertTrue($state->getRemainTick() === 5);
			$state->setRemainTick(8);
		});

		$this->on(2, function() use ($state): void {
			$this->assertTrue($state->getRemainTick() === 7);
			$state->setRemainTick(1);
		});

		$this->on(4, function() use ($state): void {
			var_dump($state->getRemainTick());
			$this->assertFalse($this->monster->getStates()->hasEqual($state));
		});
	}

	public function reset(): void {
		$this->monster->getStates()->removeAll();
	}

	public function setUpWithParticipant(TestCaseParticipant $participant): void {
		$this->monster = MonsterPlayer::get($participant->getPlayer());
	}
}
