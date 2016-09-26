<?php

/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */


namespace ElasticsearchAdapter\UserQuery;


//use VuFindSearch\ParamBag;


use ElasticsearchAdapter\UserQuery\ESParamInterface;

class ESParamBag implements ESParamInterface
{


    //this implementation stems from VuFind
    //we have to see what is necessary


    /**
     * Parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor.
     *
     * @param array $initial Initial parameters
     *
     * @return void
     */
    public function __construct(array $initial = [])
    {
        foreach ($initial as $name => $value) {
            $this->add($name, $value);
        }
    }

    /**
     * Return parameter value.
     *
     * @param string $name Parameter name
     *
     * @return mixed|null Parameter value or NULL if not set
     */
    public function get($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * Return true if the bag contains a parameter-value-pair.
     *
     * @param string $name  Parameter name
     * @param string $value Parameter value
     *
     * @return boolean
     */
    public function contains($name, $value)
    {
        $haystack = $this->get($name);
        return is_array($haystack) && in_array($value, $haystack);
    }

    /**
     * Set a parameter.
     *
     * @param string $name  Parameter name
     * @param string $value Parameter value
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $this->params[$name] = $value;
        } else {
            $this->params[$name] = [$value];
        }
    }

    /**
     * Remove a parameter.
     *
     * @param string $name Parameter name
     *
     * @return void
     */
    public function remove($name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }

    /**
     * Add parameter value.
     *
     * @param string $name  Parameter name
     * @param mixed  $value Parameter value
     *
     * @return void
     */
    public function add($name, $value)
    {
        if (!isset($this->params[$name])) {
            $this->params[$name] = [];
        }
        if (is_array($value)) {
            $this->params[$name] = array_merge($this->params[$name], $value);
        } else {
            $this->params[$name][] = $value;
        }
    }

    /**
     * Merge with another parameter bag.
     *
     * @param ESParamBag $bag Parameter bag to merge with
     *
     * @return void
     */
    public function mergeWith(ESParamBag $bag)
    {
        foreach ($bag->params as $key => $value) {
            if (!empty($value)) {
                $this->add($key, $value);
            }
        }
    }

    /**
     * Merge with all supplied parameter bags.
     *
     * @param array $bags Parameter bags to merge with
     *
     * @return void
     */
    public function mergeWithAll(array $bags)
    {
        foreach ($bags as $bag) {
            $this->mergeWith($bag);
        }
    }

    /**
     * Return copy of parameters as array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->params;
    }

    /**
     * Exchange the parameter array.
     *
     * @param array $input New parameters
     *
     * @return array Old parameters
     */
    public function exchangeArray(array $input)
    {
        $current = $this->params;
        $this->params = [];
        foreach ($input as $key => $value) {
            $this->set($key, $value);
        }
        return $current;
    }




    /**
     * Return ES DSL specific array of params ready to be used in a HTTP request.
     *
     * @return array
     */
    public function request()
    {
        $request = [];
        foreach ($this->params as $name => $values) {
            if (!empty($values)) {
                $request = array_merge(
                    $request,
                    array_map(
                        function ($value) use ($name) {
                            return sprintf(
                                '%s=%s', urlencode($name), urlencode($value)
                            );
                        },
                        $values
                    )
                );
            }
        }
        return $request;
    }



}