<?php

namespace Dte\BtsBundle\Tests\Unit\DependencyInjection;

use Dte\BtsBundle\DependencyInjection\DteBtsExtension;

class DteBtsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new DteBtsExtension();
        $configs = array();
        $isCalled = false;
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->once())
            ->method('setParameter')
            ->with($this->equalTo('dte_bts.noreply_email'), $this->equalTo('noreply@dte-bts.dev'));

        $extension->load($configs, $container);
    }
}