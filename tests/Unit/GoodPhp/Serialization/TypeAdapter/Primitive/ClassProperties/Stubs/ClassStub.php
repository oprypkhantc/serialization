<?php

namespace Tests\Unit\GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Stubs;

use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\SerializedName;

/**
 * @template T
 */
class ClassStub
{
	/**
	 * @param T $generic
	 */
	public function __construct(
		public int $primitive,
		public NestedStub $nested,
		#[SerializedName('date')]
		public mixed $generic,
	) {
	}
}
