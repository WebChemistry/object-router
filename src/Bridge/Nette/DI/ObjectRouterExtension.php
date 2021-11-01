<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Bridge\Nette\DI;

use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use WebChemistry\ObjectRouter\Bridge\Nette\Latte\ObjectRouterMacros;
use WebChemistry\ObjectRouter\ObjectRouterComposite;
use WebChemistry\ObjectRouter\ObjectRouterInterface;

final class ObjectRouterExtension extends CompilerExtension
{

	private ServiceDefinition $router;

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$this->router = (new ServiceDefinition())
			->setType(ObjectRouterInterface::class)
			->setFactory(ObjectRouterComposite::class);

		$builder->addDefinition($this->prefix('router'), $this->router);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$routers = [];
		foreach ($builder->findByType(ObjectRouterInterface::class) as $definition) {
			if ($definition === $this->router) {
				continue;
			}

			$definition->setAutowired(false);

			$routers[] = $definition;
		}

		$this->router->setArguments([$routers]);

		$service = $builder->getDefinitionByType(LatteFactory::class);
		assert($service instanceof FactoryDefinition);

		$service->getResultDefinition()
			->addSetup(
				sprintf(
					'?->onCompile[] = fn ($engine) => %s::install($engine->getCompiler())',
					ObjectRouterMacros::class
				),
				['@self']
			)
			->addSetup('addProvider', ['objectRouter', $this->router]);
	}

}
