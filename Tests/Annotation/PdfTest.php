<?php

namespace Ps\PdfBundle\Test\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Ps\PdfBundle\Annotation\Pdf;

class PdfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @Pdf()
     */
    public function testPdfAnnotationIsCorrectlyCreatedByReader()
    {
        $reader = new AnnotationReader();

        $method = new \ReflectionMethod($this, 'testPdfAnnotationIsCorrectlyCreatedByReader');
        $pdf = $reader->getMethodAnnotation($method, 'Ps\PdfBundle\Annotation\Pdf');

        $this->assertNotNull($pdf);
    }

    /**
     * @test
     * @dataProvider createAnnotationProvider
     */
    public function createAnnotation(array $args, $expectedException)
    {
        try {
            $defaultArgs = ['stylesheet' => null, 'documentParserType' => 'xml', 'headers' => [], 'enableCache' => false];

            $annotation = new Pdf($args);

            if ($expectedException) {
                $this->fail('exception expected');
            }

            $expectedVars = $args + $defaultArgs;

            $this->assertSame($expectedVars, get_object_vars($annotation));
        } catch (\InvalidArgumentException $e) {
            if (!$expectedException) {
                $this->fail('unexpected exception');
            }
        }
    }

    public function createAnnotationProvider()
    {
        return [
            [[], false],
            [['stylesheet' => 'stylesheet', 'documentParserType' => 'markdown'], false],
            [['enableCache' => true, 'headers' => ['key' => 'value']], false],
            [['unexistedArg' => 'value'], true],
        ];
    }
}
