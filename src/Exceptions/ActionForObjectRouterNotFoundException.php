<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Exceptions;

use Exception;
use Throwable;

final class ActionForObjectRouterNotFoundException extends Exception
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
				'Action "%s" for object "%s" not found.',
				$action === null ? 'NULL' : $action,
				get_debug_type($object),
			)
		);
	}

}
