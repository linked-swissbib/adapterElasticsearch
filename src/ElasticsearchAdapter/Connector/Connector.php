<?php
namespace ElasticsearchAdapter\Connector;

use ElasticsearchAdapter\Result\Result;
use ElasticsearchAdapter\Search\Search;

/**
 * Connector interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Connector
{
    public function send(Search $params) : Result;
}
