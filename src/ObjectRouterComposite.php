<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter;

use WebChemistry\ObjectRouter\Exceptions\ObjectRouterNotFoundException;

final class ObjectRouterComposite implements ObjectRouterInterface
{

	/**
	 * @param ObjectRouterInterface[] $routers
	 */
	public function __construct(
		private array $routers = [],
	)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function supports(object $object, ?string $action = null, array $context = []): bool
	{
		foreach ($this->routers as $router) {
			if ($router->supports($object, $action, $context)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function route(object $object, ?string $action = null, array $context = []): string
	{
		foreach ($this->routers as $router) {
			if ($router->supports($object, $action, $context)) {
				return $router->route($object, $action, $context);
			}
		}

		throw new ObjectRouterNotFoundException($object, $action, $context);
	}

}
