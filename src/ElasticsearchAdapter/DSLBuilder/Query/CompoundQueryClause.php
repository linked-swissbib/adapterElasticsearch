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



abstract class CompoundQueryClause extends Query
{

    protected $clauses = [];


    public function addClause(Query $clauseQuery)
    {
        $this->clauses[] = $clauseQuery;
    }

    public function getClause()
    {
        //todo: do we need this?
        throw new \Exception ('method ' . __FUNCTION__ . 'not implemented so far');
    }


    public function removeClause ()
    {
        //todo: do we need this?
        throw new \Exception('method ' . __FUNCTION__ . 'not implemented so far');
    }

}