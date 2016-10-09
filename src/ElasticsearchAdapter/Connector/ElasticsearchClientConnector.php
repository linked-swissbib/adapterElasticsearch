<?php
namespace ElasticsearchAdapter\Connector;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 * Elasticsearch client connector
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ElasticsearchClientConnector implements Connector
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param array $hosts
     */
    public function __construct(array $hosts)
    {
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    public function send(array $params) : array
    {
        return $this->client->search($params);
    }
}
