<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter\DSLBuilder\Query;


use ONGR\ElasticsearchDSL\Query\MatchAllQuery;

class Should extends BooleanQuery
{


    protected $shouldClauses = [];


    /**
     * @return array
     */
    public function build()
    {

        $collectedQueries = null;

        foreach ($this->registeredQueryClasses as $key => $boolClause)
        {
            if (array_key_exists($key,$this->spec))
            {
                /** @var Query $queryClass */

                $matchAll = new  MatchAllQuery();

                $queryClass = new $boolClause($this->query, $this->spec[$key]);
                $this->addClause($queryClass);
            }
        }

        /** @var Query $clause */
        foreach ($this->shouldClauses as $clause)
        {
            $collectedQueries['should'][] = $clause->build();
        }

        return $collectedQueries;

    }

    public function addClause(Query $clauseQuery)
    {
        $this->shouldClauses[] = $clauseQuery;
    }


}