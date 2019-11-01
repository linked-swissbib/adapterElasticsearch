<?php
namespace ElasticsearchAdapter\Search;

use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Params\ParamsReplacer;
use ElasticsearchAdapter\Query\TemplateQuery;
use ElasticsearchAdapter\Query\Query;

/**
 * TemplateRequest
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateSearch implements Search
{
    /**
     * @var string
     */
    protected $index = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $from;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var array
     */
    protected $template = [];


    /*
    private $mapType2Index = [
        'bibliographicResource' => 'bibliographicResource',
        'document' => 'document',
        'work' => 'work',
        'person' => 'person',
        'organisation' => 'organisation',
        'item' => 'item',
        //DEFAULT seems only to be used for gnd
        'DEFAULT' => 'gnd'
    ];
    */

    protected $mapType2Index = [];

    /**
     * @var Params
     */
    protected $params;

    /**
     * @var ParamsReplacer
     */
    protected $paramsReplacer;

    /**
     * @param array $template
     * @param Params $params
     */
    public function __construct(array $template, array $type2IndexMapping, Params $params = null)
    {
        $this->template = $template;
        $this->mapType2Index = $type2IndexMapping;
        $this->params = $params;
        $this->paramsReplacer = new ParamsReplacer($params);
    }

    /**
     * @return void
     */
    public function prepare()
    {
        $this->query = new TemplateQuery($this->template, $this->params);

        if (isset($this->template['index'])) {
            $this->index = $this->paramsReplacer->replace($this->template['index']);
        }

        if (isset($this->template['type'])) {
            $this->type = $this->paramsReplacer->replace($this->template['type']);
        }

        if (isset($this->template['size'])) {
            $this->size = (int) $this->paramsReplacer->replace($this->template['size']);
        }

        if (isset($this->template['from'])) {
            $this->from = (int) $this->paramsReplacer->replace($this->template['from']);
        }
    }

    /**
     * @param string $index
     */
    public function setIndex(string $index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getIndex() : string
    {
        return $this->index;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize() : int
    {
        return (int) $this->size;
    }

    /**
     * @param int $from
     */
    public function setFrom(int $from)
    {
        $this->from = $from;
    }

    /**
     * @return int
     */
    public function getFrom() : int
    {
        return (int) $this->from;
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return Query
     */
    public function getQuery() : Query
    {
        return $this->query;
    }

    /**
     * @throws \Exception
     * @return array
     */
    public function toArray() : array
    {
        $search = [
            'track_total_hits' =>  true,

            //'index' => $this->index,
            'index' => $this->adjustIndexName($this->type),
            //'type' => $this->type,
            'body' => $this->query->toArray(),
        ];

        if ($this->size !== null) {
            $search['size'] = $this->size;
        }

        if ($this->from !== null) {
            $search['from'] = $this->from;
        }

        return $search;
    }

    /**
     * maps an index-type (used in Elasticsearch version <=  5) to an index name
     * @param string $typeName
     * @throws \Exception
     * @return string
     */
    private function adjustIndexName(string $typeName) {
        if (array_key_exists($typeName, $this->mapType2Index)) {
            return $this->mapType2Index[$typeName];
        } else {
            throw new \Exception("type not found");
        }
    }
}
