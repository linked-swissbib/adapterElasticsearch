<?php
namespace Tests\ElasticsearchAdapter\Connector;

use ElasticsearchAdapter\Connector\ElasticsearchClientConnector;
use ElasticsearchAdapter\Search\TemplateSearch;
use PHPUnit\Framework\TestCase;

/**
 * ElasticsearchClientResultTest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ElasticsearchClientConnectorTest extends TestCase
{
    /**
     * @return void
     *
     * @expectedException \Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function testSend()
    {
        $connector = new ElasticsearchClientConnector(['somehostnoonehasconfigured']);
        $templateSearch = new TemplateSearch(['index' => 'test', 'type' => 'test']);

        $templateSearch->prepare();

        $connector->send($templateSearch);
    }
}
