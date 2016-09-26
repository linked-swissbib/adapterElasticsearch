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


class MultiMatchQuery extends LeafQueryClause
{

    /**
     * @return array
     * @throws \Exception
     */
    public function build()
    {


        if (!array_key_exists('fields',$this->spec)) {
            //todo: or do we use some kind of default fields?
            throw new \Exception('missing fields definition in multi_match clause');
        }

        $queryClause['multi_match']['fields'] = $this->spec['fields'];
        if (array_key_exists('type',$this->spec))
        {
            $queryClause['multi_match']['type'] = $this->spec['type'];
        }

        $queryClause['multi_match']['operator'] = isset($this->spec['operator']) ? $this->spec['operator'] : 'and';

        $queryClause['multi_match']['query'] = $this->query->getAllTerms();

        return $queryClause;

    }



}