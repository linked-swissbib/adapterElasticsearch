<?php
/**
 * Created by PhpStorm.
 * User: swissbib
 * Date: 16.09.16
 * Time: 15:35
 */

namespace ElasticsearchAdapter\UserQuery;


interface ESParamInterface
{

    //VuFind uses the Param type mainly for initialization with values from the HTTP-Request object
    //do we need something similar?
    //if we want to use the component as Adapter for an ES Backend part of the VuFind universe than it is necessary
    //for Hydra we can wait - I guess...

}