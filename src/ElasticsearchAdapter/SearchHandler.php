<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticsearchAdapter;



/*
 * SearchHandler is a facade for the concrete configured SearchSpec to be used
 * by the concrete request
 */
class SearchHandler
{


    /**
     * Known configuration keys.
     *
     * @var array
     */
    //protected static $configKeys = [
    //    'CustomMunge', 'DismaxFields', 'DismaxHandler', 'QueryFields',
    //    'DismaxParams', 'FilterQuery'
    //];

    protected static $configKeys = [

        'UsedTypes', 'UsedIndex', 'Multisearch','nested',
         'fields', 'query',
        'bool', 'should', 'multi_match', 'type',
        'operator', 'path',

    ];



    /**
     * Search handler specification.
     *
     * @var array
     */
    protected $specs = [];

    /**
     * Constructor.
     *
     * @param array  $specs                Search handler specifications
     * @param string $defaultDismaxHandler Default dismax handler (if no
     * DismaxHandler set in specs).
     *
     * @return void
     */
    public function __construct(array $spec = [],
                                $defaultDismaxHandler = 'dismax') {
        foreach (self::$configKeys as $key) {
            $this->specs[$key] = isset($spec[$key]) ? $spec[$key] : [];
        }
        // Set dismax handler to default if not specified:
        if (empty($this->specs['DismaxHandler'])) {
            $this->specs['DismaxHandler'] = $defaultDismaxHandler;
        }
    }



    /**
     * Return defined dismax fields.
     *
     * @return array
     */
    public function getDismaxFields()
    {
        //return $this->specs['DismaxFields'];
        //only a quick patch to provide Chur a quick solution without error
        return $this->specs['query']['bool']['should']['multi_match']['fields'];
    }

    /**
     * Return defined dismax fields.
     *
     * @return array
     */
    public function getIndices()
    {
        //return [];
        return $this->specs['UsedIndex'];
    }


    /**
     * Return the types (as part of the index) defined for this specific search handler .
     *
     * @return array
     */
    public function getTypes()
    {
        //todo: implement this -> we need this to build the query!
        return $this->specs['UsedTypes'];
    }

    public function getQuery()
    {
        return $this->specs['query'];
    }

    public function getSpec()
    {
        return $this->specs;
    }


}