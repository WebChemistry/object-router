<?php declare(strict_types = 1);

namespace WebChemistry\ObjectRouter\Bridge\Nette\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class ObjectRouterMacros extends MacroSet
{

	public static function install(Compiler $compiler): self
	{
		$me = new self($compiler);
		$me->addMacro('ohref', null, null, [$me, 'oHrefAttr']);
		$me->addMacro('olink', [$me, 'oLinkBegin'], null, [$me, 'oHrefAttr']);

		return $me;
	}

	public function oLinkBegin(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write('%node.line echo $this->global->objectRouter->route(%node.args);');
	}

	public function oHrefAttr(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write(
			'%node.line echo \' href="\'; echo $this->global->objectRouter->route(%node.args); echo \'"\';'
		);
	}

}
