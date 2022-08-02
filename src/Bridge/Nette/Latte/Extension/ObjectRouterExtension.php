<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Bridge\Nette\Latte\Extension;

use Latte\Extension;
use WebChemistry\ObjectRouter\Bridge\Nette\Latte\Extension\Node\OHrefNode;
use WebChemistry\ObjectRouter\ObjectRouterInterface;

final class ObjectRouterExtension extends Extension
{

	public function __construct(
		private ObjectRouterInterface $objectRouter,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function getProviders(): array
	{
		return [
			'objectRouter' => $this->objectRouter,
		];
	}

	/**
	 * @return mixed[]
	 */
	public function getTags(): array
	{
		return [
			'ohref' => [OHrefNode::class, 'create'],
			'n:ohref' => [OHrefNode::class, 'create'],
		];
	}

}
