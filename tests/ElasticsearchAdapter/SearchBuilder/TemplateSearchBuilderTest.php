<?php
namespace Tests\ElasticsearchAdapter\QueryBuilder;

use ElasticsearchAdapter\Params\ArrayParams;
use ElasticsearchAdapter\SearchBuilder\TemplateSearchBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * TemplateSearchBuilderTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateSearchBuilderTest extends TestCase
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var TemplateSearchBuilder
     */
    protected $queryBuilder;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->templates = Yaml::parse($this->loadResource('templates.yml'));
        $this->queryBuilder = new TemplateSearchBuilder($this->templates, new ArrayParams());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testInvalidTemplateName()
    {
        $search = $this->queryBuilder->buildSearchFromTemplate('no one would call a template like that');
    }

    /**
     * @return void
     */
    public function testMatchTemplate()
    {
        $search = $this->queryBuilder->buildSearchFromTemplate('match');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => [
                            'query' => 'test query'
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testMatchTemplateWithVariables()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('q')->willReturn(true);
        $paramsProphecy->get('q')->willReturn('the query string');

        $this->queryBuilder->setParams($paramsProphecy->reveal());
        $search = $this->queryBuilder->buildSearchFromTemplate('match_with_variables');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => [
                            'query' => 'the query string'
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function loadResource(string $fileName) : string
    {
        $filePath = __DIR__ . '/../../Resources/' . $fileName;

        return file_get_contents($filePath);
    }
}
