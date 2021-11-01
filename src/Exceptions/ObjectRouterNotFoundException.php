<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Exceptions;

use Exception;

final class ObjectRouterNotFoundException extends Exception
{

	/**
	 * @param mixed[] $context
	 */
	public function __construct(
		object $object,
		?string $action,
		public array $context = [],
	)
	{
		parent::__construct(
			sprintf(
				'Router for object "%s" and action "%s" not found.',
				get_debug_type($object),
				$action === null ? 'NULL' : $action,
			)
		);
	}

}
