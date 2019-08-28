<?php
namespace ElasticsearchAdapter\Result;

/**
 * TemplateQuery
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ElasticsearchClientResult implements Result
{
    /**
     * @var array
     */
    protected $result;

    /**
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getTotal() : int
    {
        //changed structure in ES7 and not always available (depends on query)- therefor: check it
        if (array_key_exists('hits', $this->result) && array_key_exists('total', $this->result['hits']) &&
            array_key_exists('value', $this->result['hits']['total']))
            return $this->result['hits']['total']['value'];
        else
            return 0;

    }

    /**
     * @return int
     */
    public function getTook() : int
    {
        return $this->result['took'];
    }

    /**
     * @return bool
     */
    public function getTimedOut() : bool
    {
        return $this->result['timed_out'];
    }

    /**
     * @return float
     */
    public function getMaxScore() : float
    {
        return $this->result['hits']['max_score'];
    }

    /**
     * @return array
     */
    public function getHits() : array
    {
        return $this->result['hits']['hits'] ?? [];
    }

    /**
     * @return array
     */
    public function getRawResult() : array
    {
        return $this->result;
    }
}
