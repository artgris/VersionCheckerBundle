<?php


namespace Artgris\VersionCheckerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('artgris_version_checker');
		$rootNode
			->children()
			->scalarNode("access_token")->defaultValue(null)->end()
			->scalarNode("lifetime")->defaultValue(3600)->end()
			->end();
		return $treeBuilder;
	}
}