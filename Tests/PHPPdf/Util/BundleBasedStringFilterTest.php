<?php

namespace Ps\PdfBundle\Tests\PHPPdf\Util;

use Ps\PdfBundle\PHPPdf\Util\BundleBasedStringFilter;

class BundleBasedStringFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;
    private $kernel;

    public function setUp()
    {
        $this->kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock();
        $this->filter = new BundleBasedStringFilter($this->kernel);
    }

    /**
     * @test
     * @dataProvider replaceBundleVariablesProvider
     */
    public function replaceBundleVariables($string, $expectedString, array $expectedBundles)
    {
        foreach ($expectedBundles as $at => $bundle) {
            list($bundleName, $bundlePath) = $bundle;

            $bundleMock = $this->getMockBuilder('\Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
            $bundleMock->expects($this->once())
                       ->method('getPath')
                       ->will($this->returnValue($bundlePath));

            $this->kernel->expects($this->at($at))
                         ->method('getBundle')
                         ->with($bundleName)
                         ->will($this->returnValue($bundleMock));
        }

        $actualString = $this->filter->filter($string);

        $this->assertSame($expectedString, $actualString);
    }

    public function replaceBundleVariablesProvider()
    {
        return [
            ['some text', 'some text', []],
            ['text text %SomeBundle:file.xml% text text', 'text text path/Resources/file.xml text text', [
                ['SomeBundle', 'path'],
            ]],
            ['text text %SomeBundle:file1.xml% text %SomeBundle:file2.xml% text', 'text text path/Resources/file1.xml text path/Resources/file2.xml text', [
                ['SomeBundle', 'path'],
                ['SomeBundle', 'path'],
            ]],
        ];
    }
}
