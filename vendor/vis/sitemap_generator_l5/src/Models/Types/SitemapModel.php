<?php

namespace Vis\SitemapGenerator;

/**
 * Class SitemapModel
 * @package Vis\SitemapGenerator
 */
class SitemapModel extends AbstractSitemapObject
{
    /**
     * Targeted model property
     * @var
     */
    protected $model;

    /**
     * Defines specific object of targeted model
     * @var
     */
    protected $entity;

    /**
     * Defines lastmod field of targeted model
     * @var string
     */
    protected $lastmod_field = "updated_at";

    /**
     * Defines URL retrieving method of targeted model
     * @var string
     */
    protected $url_method = "getUrl";

    /**
     * Defines activity field of targeted model
     * @var string
     */
    protected $active_field = 'is_active';

    /**
     * Defines list of additional query filters of targeted model
     * @var array
     */
    protected $additional_where = [];

    /**
     * Sets config key to model property
     * @param string $key
     * @return $this
     */
    protected function setKey($key)
    {
        $this->model = $key;
        return $this;
    }

    /**
     * Sets URL property to retrieved from targeted model
     * @return $this
     */
    protected function setUrl()
    {
        $field = $this->url_method;
        $this->url = $field ? $this->entity->$field() : "/";

        return $this;
    }

    /**
     * Sets lastmod property to retrieved from targeted model
     * @return $this
     */
    protected function setLastmod()
    {
        $field = $this->lastmod_field;
        $this->lastmod = $field ? $this->entity->$field : "";

        return $this;
    }

    /**
     * Gets additional_where property
     * @return array
     */
    protected function getAdditionalWhere()
    {
        return $this->additional_where;
    }

    /**
     * Gets active_field property
     * @return string
     */
    protected function getActiveField()
    {
        return $this->active_field;
    }

    /**
     * Applies additional_wheres to targeted model query
     */
    private function applyAdditionalWheres()
    {
        foreach ($this->getAdditionalWhere() as $fieldName => $condition) {

            $condition['sign'] = strtoupper($condition['sign']);

            switch ($condition['sign']) {
                case "IN":
                    $this->model = $this->model->whereIn($fieldName, $condition['value']);
                    break;
                case 'NOT IN':
                    $this->model = $this->model->whereNotIn($fieldName, $condition['value']);
                    break;
                case "BETWEEN":
                    $this->model = $this->model->whereBetween($fieldName, $condition['value']);
                    break;
                case 'NOT BETWEEN':
                    $this->model = $this->model->whereNotBetween($fieldName, $condition['value']);
                    break;
                case "NULL":
                    $this->model = $this->model->whereNull($fieldName);
                    break;
                case "NOT NULL":
                    $this->model = $this->model->whereNotNull($fieldName);
                    break;
                default:
                    $this->model = $this->model->where($fieldName, $condition['sign'], $condition['value']);
                    break;
            }
        }
    }

    /**
     * Gets entities from targeted model and adds them to links array
     * @return array $links
     */
    public function getLinksArray()
    {
        $links = [];

        $this->model = new $this->model;

        if ($this->getActiveField()) {
            $this->model = $this->model->where($this->getActiveField(), "=", 1);
        }

        $this->applyAdditionalWheres();

        $entities = $this->model->get();

        foreach ($entities as $this->entity) {
            $links[] = $this->setUrl()->setLastmod()->convertToArray();
        }

        return $links;
    }

}
