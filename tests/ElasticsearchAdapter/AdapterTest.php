<?php
namespace Tests\ElasticsearchAdapter;

use ElasticsearchAdapter\Adapter;
use ElasticsearchAdapter\Connector\Connector;
use ElasticsearchAdapter\Result\Result;
use ElasticsearchAdapter\Search\Search;
use PHPUnit\Framework\TestCase;

/**
 * AdapterTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class AdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function testSearch()
    {
        $searchProphecy = $this->prophesize(Search::class);
        $search = $searchProphecy->reveal();
        $resultProphecy = $this->prophesize(Result::class);
        $result = $resultProphecy->reveal();
        $connectorProphecy = $this->prophesize(Connector::class);
        $connectorProphecy->send($search)->willReturn($result);

        $adapter = new Adapter($connectorProphecy->reveal());

        $this->assertEquals($result, $adapter->search($search));
    }
}
