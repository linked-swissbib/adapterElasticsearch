<?php
namespace ElasticsearchAdapter\Query;

use ElasticsearchAdapter\Params\Params;

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
    public function toArray() : array;

    /**
     * @return void
     */
    public function build();

    /**
     * @param Params $params
     */
    public function setParams(Params $params);

    /**
     * @return Params
     */
    public function getParams();
}
