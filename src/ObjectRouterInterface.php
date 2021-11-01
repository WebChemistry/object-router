<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter;

interface ObjectRouterInterface
{

	/**
	 * @param mixed[] $context
	 */
	public function supports(object $object, ?string $action = null, array $context = []): bool;

	/**
	 * @param mixed[] $context
	 */
	public function route(object $object, ?string $action = null, array $context = []): ?string;

}
