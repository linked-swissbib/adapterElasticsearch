<?php
namespace ElasticsearchAdapter\QueryBuilder;

use ElasticsearchAdapter\Params\ArrayParams;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * TemplateQueryBuilder interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateQueryBuilderTest extends TestCase
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var TemplateQueryBuilder
     */
    protected $queryBuilder;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->templates = Yaml::parse($this->loadResource('templates.yml'));
        $this->queryBuilder = new TemplateQueryBuilder($this->templates, new ArrayParams());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testInvalidTemplateName()
    {
        $query = $this->queryBuilder->buildQueryFromTemplate('no one would call a template like that');
    }

    /**
     * @return void
     */
    public function testMatchTemplate()
    {
        $query = $this->queryBuilder->buildQueryFromTemplate('match');

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

        $this->assertEquals($expected, $query->getQuery());
    }

    /**
     * @return void
     */
    public function testMatchTemplateWithVariables()
    {
        $params = new ArrayParams();
        $params->set('q', 'the query string');

        $this->queryBuilder->setParams($params);
        $query = $this->queryBuilder->buildQueryFromTemplate('match_with_variables');

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

        $this->assertEquals($expected, $query->getQuery());
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
