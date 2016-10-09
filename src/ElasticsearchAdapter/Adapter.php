<?php
namespace ElasticsearchAdapter;

use ElasticsearchAdapter\Config\Config;
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
     * @var Config
     */
    protected $config;

    /**
     * Adapter constructor.
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param Query $query
     * @param Params $params
     *
     * @return array
     */
    public function search(Query $query, Params $params) : array
    {
        return [];
    }
}
