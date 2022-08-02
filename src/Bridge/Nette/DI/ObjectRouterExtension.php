<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Bridge\Nette\DI;

use Latte\Engine;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use WebChemistry\ObjectRouter\Bridge\Nette\Latte\Extension\ObjectRouterExtension as LatteObjectRouterExtension;
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

		if (version_compare(Engine::VERSION, '3', '<')) {
			$this->loadLatte2($builder);
		} else {
			$this->loadLatte3($builder);
		}
	}

	private function loadLatte2(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$service = $builder->getDefinition($serviceName);
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

	private function loadLatte3(ContainerBuilder $builder): void
	{
		$serviceName = $builder->getByType(LatteFactory::class);
		if (!$serviceName) {
			return;
		}

		$extension = $builder->addDefinition($this->prefix('latte.extension'))
			->setFactory(LatteObjectRouterExtension::class, [$this->router]);

		$factory = $builder->getDefinition($serviceName);
		assert($factory instanceof FactoryDefinition);

		$factory->getResultDefinition()
			->addSetup('addExtension', [$extension]);
	}

}
