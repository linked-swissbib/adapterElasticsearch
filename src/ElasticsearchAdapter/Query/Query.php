<?php
namespace ElasticsearchAdapter\Query;

/**
 * Query interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Query
{
    /**
     * @return array
     */
    public function getQuery() : array;
}
