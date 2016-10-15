<?php
namespace ElasticsearchAdapter;

use ElasticsearchAdapter\Config\Config;
use ElasticsearchAdapter\Connector\Connector;
use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Query\Query;

/**
 * ElasticsearchAdapter
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class Adapter
{
    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @param Connector $connector
     */
    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param Query $query
     * @param Params $params
     *
     * @return array
     */
    public function search(Query $query, Params $params) : array
    {
        return $this->connector->send($query->getQuery());
    }
}
