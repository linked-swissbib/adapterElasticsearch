<?php
namespace ElasticsearchAdapter\QueryBuilder;

use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Query\TemplateQuery;
use ElasticsearchAdapter\Query\Query;
use InvalidArgumentException;

/**
 * TemplateQueryBuilder interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateQueryBuilder implements QueryBuilder
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var Params
     */
    protected $params;

    /**
     * @param array $templates
     * @param Params $params
     */
    public function __construct(array $templates, Params $params = null)
    {
        $this->templates = $templates;
        $this->params = $params;
    }

    /**
     * @param string $template
     *
     * @return Query
     *
     * @throws InvalidArgumentException if template is not found
     */
    public function buildQueryFromTemplate(string $template) : Query
    {
        if (!isset($this->templates[$template])) {
            throw new InvalidArgumentException('No template with name "' . $template . '" found.');
        }

        $templateQuery = new TemplateQuery($this->templates[$template]);

        if ($this->params !== null) {
            $templateQuery->setParams($this->params);
        }

        $templateQuery->build();

        return $templateQuery;
    }

    /**
     * @return Params
     */
    public function getParams(): Params
    {
        return $this->params;
    }

    /**
     * @param Params $params
     */
    public function setParams(Params $params)
    {
        $this->params = $params;
    }
}
