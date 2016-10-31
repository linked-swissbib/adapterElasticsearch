<?php
namespace Tests\ElasticsearchAdapter\Search;

use ElasticsearchAdapter\Params\ArrayParams;
use ElasticsearchAdapter\Query\Query;
use ElasticsearchAdapter\Search\TemplateSearch;
use ElasticsearchAdapter\SearchBuilder\TemplateSearchBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * TemplateSearchTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateSearchTest extends TestCase
{
    /**
     * @var array
     */
    protected $template = [
        'index' => 'testIndex',
        'type' => 'testType',
        'size' => 100,
        'from' => 0,
    ];

    /**
     * @return void
     */
    public function testTemplateSearch()
    {
        $templateSearch = new TemplateSearch($this->template);
        $templateSearch->prepare();
        $queryProphecy = $this->prophesize(Query::class);
        $query = $queryProphecy->reveal();

        $this->assertEquals('testIndex', $templateSearch->getIndex());
        $this->assertEquals('testType', $templateSearch->getType());
        $this->assertEquals(100, $templateSearch->getSize());
        $this->assertEquals(0, $templateSearch->getFrom());
        $this->assertNotNull($templateSearch->getQuery());

        $templateSearch->setIndex('testIndex2');
        $templateSearch->setType('testType2');
        $templateSearch->setSize(200);
        $templateSearch->setFrom(190);
        $templateSearch->setQuery($query);

        $this->assertEquals('testIndex2', $templateSearch->getIndex());
        $this->assertEquals('testType2', $templateSearch->getType());
        $this->assertEquals(200, $templateSearch->getSize());
        $this->assertEquals(190, $templateSearch->getFrom());
        $this->assertEquals($query, $templateSearch->getQuery());
    }
}
