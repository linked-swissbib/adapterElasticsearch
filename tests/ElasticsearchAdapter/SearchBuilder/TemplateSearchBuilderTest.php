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
    public function testMatchTemplateWithParameters()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('match_with_parameters');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'match' => [
                        '_all' => [
                            'query' => 'search query',
                            'operator' => 'and',
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     *
     * @expectedException \ElasticsearchAdapter\Exception\RequiredParameterException
     */
    public function testMatchTemplateWithParametersMissingParameter()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('match_with_parameters_missing_parameter');

        $search->toArray();
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
    public function testIdsTemplateWithParameters()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('ids_with_parameters');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'ids' => [
                        'type' => 'typeB',
                        'values' => [
                            0 => '1234',
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
     * @return void
     */
    public function testMultiMatchWithVariablesTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('q')->willReturn(true);
        $paramsProphecy->get('q')->willReturn('test query');
        $paramsProphecy->has('firstName')->willReturn(true);
        $paramsProphecy->get('firstName')->willReturn('my first name');
        $paramsProphecy->has('lastName')->willReturn(true);
        $paramsProphecy->get('lastName')->willReturn('my last name');
        $paramsProphecy->has('address')->willReturn(true);
        $paramsProphecy->get('address')->willReturn('my address, should not be set');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('multi_match_with_variables');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => [
                            'my first name',
                            'my last name',
                            'address'
                        ],
                        'query' => 'test query',
                    ],
                ],
            ],
            'size' => 20,
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testMultiMatchTemplate()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('multi_match');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => [
                            'field1^2',
                            'field2',
                        ],
                        'query' => 'test',
                        'type' => 'cross_fields',
                        'operator' => 'and',
                        'minimum_should_match' => '50%',
                    ],
                ],
            ],
            'size' => 20,
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testTermTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('test')->willReturn(true);
        $paramsProphecy->get('test')->willReturn('test term');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('term');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'term' => [
                        'test' => 'test term',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     */
    public function testTermTemplateWithParameters()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('term_with_parameters');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'term' => [
                        'test' => [
                            'value' => 'test',
                            'boost' => 2,
                        ]
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     *
     * @expectedException \ElasticsearchAdapter\Exception\RequiredParameterException
     */
    public function testTermTemplateWithParametersMissingParameter()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('term_with_parameters_missing_parameter');

        $search->toArray();
    }

    /**
     * @return void
     */
    public function testBoolTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('test')->willReturn(true);
        $paramsProphecy->get('test')->willReturn('test field');
        $paramsProphecy->has('q')->willReturn(true);
        $paramsProphecy->get('q')->willReturn('test query');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('bool');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'multi_match' => [
                                    'query' => 'test query',
                                    'fields' => [
                                        'test field',
                                        'test2'
                                    ],
                                ]
                            ],
                        ],
                        'must_not' => [
                            [
                                'term' => [
                                    'status' => 'active',
                                ],
                            ],
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
    public function testComplexBoolTemplate()
    {
        $paramsProphecy = $this->prophesize(ArrayParams::class);
        $paramsProphecy->has('username')->willReturn(true);
        $paramsProphecy->get('username')->willReturn('my name');

        $this->searchBuilder->setParams($paramsProphecy->reveal());
        $search = $this->searchBuilder->buildSearchFromTemplate('complex_bool');

        $expected = [
            'index' => 'testIndex',
            'type' => 'testType',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'user' => 'test user',
                                ]
                            ],
                        ],
                        'must_not' => [
                            [
                                'term' => [
                                    'availability' => 'not available',
                                ],
                            ],
                            [
                                'term' => [
                                    'availability' => 'maybe available',
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'username' => 'my name',
                                ],
                            ],
                        ],
                        'should' => [
                            [
                                'term' => [
                                    'favourite' => 'is favourite',
                                ],
                            ],
                        ],
                        'boost' => 1.0,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $search->toArray());
    }

    /**
     * @return void
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidQueryTypeTemplate()
    {
        $search = $this->searchBuilder->buildSearchFromTemplate('invalid_query_type');

        $search->getQuery()->build();
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
