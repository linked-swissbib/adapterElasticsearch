<?php
namespace ElasticsearchAdapter\Query;

use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Params\ParamsReplacer;
use InvalidArgumentException;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\IdsQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;

/**
 * TemplateQuery
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateQuery implements Query
{
    /**
     * @var array
     */
    protected $template = [];

    /**
     * @var array
     */
    protected $query = null;

    /**
     * @var Search
     */
    protected $search = null;

    /**
     * @var Params
     */
    protected $params = null;

    /**
     * @var ParamsReplacer
     */
    protected $paramsReplacer = null;

    /**
     * @var array
     */
    protected $boolQueryConfigToConst = [
        'must' => BoolQuery::MUST,
        'must_not' => BoolQuery::MUST_NOT,
        'should' => BoolQuery::SHOULD,
        'filter' => BoolQuery::FILTER
    ];

    /**
     * @param array $template
     * @param Params $params
     */
    public function __construct(array $template, Params $params = null)
    {
        $this->template = $template;
        $this->params = $params;
        $this->paramsReplacer = new ParamsReplacer($params);
    }

    /**
     * @return array
     */
    public function getQuery() : array
    {
        if ($this->query === null) {
            $this->query = $this->buildQuery();
        }

        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function build()
    {
        $this->query = $this->buildQuery();
    }

    /**
     * @return Params
     */
    public function getParams() : Params
    {
        return $this->params;
    }

    /**
     * @param Params $params
     */
    public function setParams(Params $params)
    {
        $this->params = $params;

        $this->paramsReplacer->setParams($params);
    }

    public function toArray() : array
    {
        return $this->getQuery();
    }

    /**
     * @return array
     */
    protected function buildQuery()
    {
        $this->search = new Search();

        if (isset($this->template['query'])) {
            foreach ($this->template['query'] as $type => $config) {
                $this->search->addQuery($this->buildQueryClause($type, $config));
            }
        }

        return $this->search->toArray();
    }

    /**
     * @param string $queryType
     * @param array $config
     *
     * @return BuilderInterface
     */
    protected function buildQueryClause(string $queryType, array $config) : BuilderInterface
    {
        switch ($queryType) {
            case 'ids':
                return $this->buildIdsQueryClause($config);
            case 'match':
                return $this->buildMatchQueryClause($config);
            case 'multi_match':
                return $this->buildMultiMatchQueryClause($config);
            case 'bool':
                return $this->buildBoolQueryClause($config);
            case 'term':
                return $this->buildTermQueryClause($config);
            default:
                throw new InvalidArgumentException('QueryType "' . $queryType . '" is not implemented yet.');
        }
    }

    /**
     * @param array $query
     *
     * @return IdsQuery
     */
    protected function buildIdsQueryClause(array $query) : IdsQuery
    {
        $values = $this->paramsReplacer->replace($query['values']);
        $idsQuery = new IdsQuery($values);

        return $idsQuery;
    }

    /**
     * @param array $config
     *
     * @return MatchQuery
     */
    protected function buildMatchQueryClause(array $config) : MatchQuery
    {
        //todo do we need parameters? what should the template syntax be?
        $name = key($config);
        $matchQuery = new MatchQuery($name, $this->paramsReplacer->replace($config[$name]));

        return $matchQuery;
    }

    /**
     * @param array $config
     *
     * @return MultiMatchQuery
     */
    protected function buildMultiMatchQueryClause(array $config) : MultiMatchQuery
    {
        $query = $this->paramsReplacer->replace($config['query']);
        $fields = $this->paramsReplacer->replace(explode(',', $config['fields']));
        $parameters = [];

        foreach ($config as $key => $value) {
            if (!in_array($key, ['query', 'fields'])) {
                $parameters[$key] = $this->paramsReplacer->replace($value);
            }
        }

        $multiMatchQuery = new MultiMatchQuery($fields, $query, $parameters);

        return $multiMatchQuery;
    }

    /**
     * @param array $config
     *
     * @return BoolQuery
     */
    protected function buildBoolQueryClause(array $config) : BoolQuery
    {
        $boolQuery = new BoolQuery();

        foreach ($config as $key => $value) {
            if (is_string($value)) {
                $boolQuery->addParameter($key, $value);
            } else {
                $boolQueryType = $this->boolQueryConfigToConst[$key];

                foreach ($value as $type => $config) {
                    if (is_string($config)) {
                        $query = $this->buildQueryClause('term', [$type => $config]);
                    } else {
                        $query = $this->buildQueryClause($type, $config);
                    }

                    $boolQuery->add($query, $boolQueryType);
                }
            }
        }

        return $boolQuery;
    }

    /**
     * @param array $config
     *
     * @return TermQuery
     */
    protected function buildTermQueryClause(array $config) : TermQuery
    {
        $name = key($config);
        $termQuery = new TermQuery($name, $config[$name]);

        return $termQuery;
    }
}
