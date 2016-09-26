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


class Nested extends CompoundQueryClause
{

    /**
     * @return array
     * @throws \Exception
     */
    public function build()
    {

        if (!array_key_exists('query',$this->spec))
        {
            //todo: or do we use some kind of default fields?
            throw new \Exception('missing fields definition in multi_match clause');
        }
        //$queryClause = ['nested'];
        $queryClause['nested']['path'] = $this->spec['path'];

        /** @var Query $queryClass */
        $queryClass = new $this->registeredQueryClasses['query']($this->query, $this->spec);
        $queryClause['nested']['query'] = $queryClass->build();

        return $queryClause;



    }


}