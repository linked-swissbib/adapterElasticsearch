<?php
namespace ElasticsearchAdapter\Query;

use InvalidArgumentException;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\IdsQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;

/**
 * OngrQuery
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
     * @var array
     */
    protected $boolQueryConfigToConst = [
        'must' => BoolQuery::MUST,
        'must_not' => BoolQuery::MUST_NOT,
        'should' => BoolQuery::SHOULD
    ];

    /**
     * @param array $template
     */
    public function __construct(array $template)
    {
        $this->template = $template;
    }

    /**
     * @inheritdoc
     */
    public function getQuery() : array
    {
        if ($this->query === null) {
            $this->query = $this->buildQuery();
        }

        return $this->query;
    }

    /**
     * @return array
     */
    protected function buildQuery()
    {
        $this->search = new Search();

        foreach ($this->template['query'] as $type => $config) {
            $this->search->addQuery($this->buildQueryClause($type, $config));
        }

        if (isset($this->template['filter'])) {
            foreach ($this->template['filter'] as $type => $config) {
                $this->search->addFilter($this->buildFilterClause($type, $config));
            }
        }

        $searchParams = [
            'index' => $this->template['index'],
            'type' => $this->template['type'],
            'body' => $this->search->toArray(),
        ];

        return $searchParams;
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
        $idsQuery = new IdsQuery($query['values']);

        return $idsQuery;
    }

    /**
     * @param array $config
     *
     * @return MatchQuery
     */
    protected function buildMatchQueryClause(array $config) : MatchQuery
    {
        $name = key($config);
        $matchQuery = new MatchQuery($name, $config[$name]);

        return $matchQuery;
    }

    /**
     * @param array $config
     *
     * @return MultiMatchQuery
     */
    protected function buildMultiMatchQueryClause(array $config) : MultiMatchQuery
    {
        $multiMatchQuery = new MultiMatchQuery($config['query'], $config['fields']);

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

    /**
     * @param string $type
     * @param array $config
     *
     * @return BuilderInterface
     */
    protected function buildFilterClause(string $type, array $config) : BuilderInterface
    {
        return $this->buildQueryClause($type, $config);
    }
}
