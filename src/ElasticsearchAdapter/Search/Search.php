<?php
namespace ElasticsearchAdapter\Search;

use ElasticsearchAdapter\Query\Query;

/**
 * Request
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Search
{
    /**
     * @param string $index
     *
     * @return void
     */
    public function setIndex(string $index);

    /**
     * @return string
     */
    public function getIndex() : string;

    /**
     * @param string $types
     *
     * @return void
     */
    public function setType(string $types);

    /**
     * @return string
     */
    public function getType() : string;

    /**
     * @param int $size
     *
     * @return void
     */
    public function setSize(int $size);

    /**
     * @return int
     */
    public function getSize() : int;

    /**
     * @param int $from
     *
     * @return void
     */
    public function setFrom(int $from);

    /**
     * @return int
     */
    public function getFrom() : int;

    /**
     * @param Query $type
     *
     * @return void
     */
    public function setQuery(Query $type);

    /**
     * @return Query
     */
    public function getQuery() : Query;

    /**
     * @return array
     */
    public function toArray() : array;
}
