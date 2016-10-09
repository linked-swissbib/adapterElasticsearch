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
     * Adapter constructor.
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
        $builtQuery = $query->getQuery();
        $queryString = json_encode($builtQuery);

        foreach ($params as $name => $value) {
            $queryString = str_replace('{{' . $name . '}}', $value, $queryString);
        }

        $queryString = preg_replace('{{[a-z]*}}', '', preg_replace('{{[a-z]*}}', '', $queryString));
        $boundQuery = json_decode($queryString, true);

        return $this->connector->send($boundQuery);
    }
}
