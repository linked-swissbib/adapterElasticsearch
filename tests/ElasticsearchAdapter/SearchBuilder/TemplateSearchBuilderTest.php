<?php
namespace Tests\ElasticsearchAdapter\SearchBuilder;

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
    protected $searchBuilder;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->templates = Yaml::parse($this->loadResource('templates.yml'));
        $this->searchBuilder = new TemplateSearchBuilder($this->templates, new ArrayParams());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testInvalidTemplateName()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('no one would call a template like that');
    }

    /**
     * @return void
     */
    public function testParams()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $params = $paramsProphecy->reveal();

        $this->searchBuilder->setParams($params);

        $this->assertEquals($params, $this->searchBuilder->getParams());
    }

    /**
     * @return void
     */
    public function testSearch()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('type')->willReturn(true);
        $paramsProphecy->has('index')->willReturn(true);
        $paramsProphecy->has('size')->willReturn(true);
        $paramsProphecy->has('from')->willReturn(true);
        $paramsProphecy->get('type')->willReturn('test, test2');
        $paramsProphecy->get('index')->willReturn('index, index2');
        $paramsProphecy->get('size')->willReturn('10');
        $paramsProphecy->get('from')->willReturn('0');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('search');

        $expected = [
            'index' => 'index1',
            'type' => 'type1',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => [
                            'query' => 'test query'
                        ],
                    ],
                ],
            ],
            'size' => 100,
            'from' => 90,
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testSearchWithVariables()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('type')->willReturn(true);
        $paramsProphecy->has('index')->willReturn(true);
        $paramsProphecy->has('size')->willReturn(true);
        $paramsProphecy->has('from')->willReturn(true);
        $paramsProphecy->get('type')->willReturn('test, test2');
        $paramsProphecy->get('index')->willReturn('index, index2');
        $paramsProphecy->get('size')->willReturn('10');
        $paramsProphecy->get('from')->willReturn('0');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('search_with_variables');

        $expected = [
            'index' => 'index, index2',
            'type' => 'test, test2',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => [
                            'query' => 'test query'
                        ],
                    ],
                ],
            ],
            'size' => '10',
            'from' => '0',
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testMatchTemplate()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('match');

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

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('match_with_variables');

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
     * @return void
     */
    public function testIdsTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('id')->willReturn(true);
        $paramsProphecy->get('id')->willReturn('testid1234');
        $paramsProphecy->has('type')->willReturn(true);
        $paramsProphecy->get('type')->willReturn('testType');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('ids');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => [
                            0 => 'testid1234',
                            1 => '1234',
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
    public function testReplaceQueryParamsTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('id')->willReturn(true);
        $paramsProphecy->get('id')->willReturn('testid1234');
        $paramsProphecy->has('type')->willReturn(true);
        $paramsProphecy->get('type')->willReturn('testType');
        $params = $paramsProphecy->reveal();

        $search = $this->searchBuilder->buildSearchFromTemplate('params');

        $search->getQuery()->setParams($params);

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => [
                            0 => 'testid1234',
                            1 => '1234',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($params, $search->getQuery()->getParams());
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
