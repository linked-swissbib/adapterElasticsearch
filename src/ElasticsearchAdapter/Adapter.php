<?php
namespace ElasticsearchAdapter;

use ElasticsearchAdapter\Connector\Connector;
use ElasticsearchAdapter\Result\Result;
use ElasticsearchAdapter\Search\Search;

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
     * @param Search $search
     *
     * @return Result
     */
    public function search(Search $search) : Result
    {
        return $this->connector->send($search);
    }
}
