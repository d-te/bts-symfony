<?php

namespace Dte\BtsBundle\Tests\Unit\DependencyInjection;

use Dte\BtsBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);

        $root = $builder->buildTree();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\ArrayNode', $root);
        $this->assertEquals('dte_bts', $root->getName());

        $children = $root->getChildren();
        $this->assertInternalType('array', $children);
        $this->assertCount(1, $children);
    }
}
