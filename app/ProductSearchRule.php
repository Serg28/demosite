<?php

namespace App;

use Novius\ScoutElastic\SearchRule;

class ProductSearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        //Здесь нужно добавить возможность искать также по товарам с полем is_active=0
        return [
            "must" => [
                [
                    'bool' => [
                        'should' => [
                            [
                                'query_string' => [
                                    'query' => '*' . $this->prepareWord($this->builder->query) . '*',
                                    'default_operator' => 'AND',
                                    'analyze_wildcard' => true,
                                    'fields' => ["code^5", "title^2"]
                                ]
                            ],
                            [
                                'query_string' => [
                                    'query' => $this->prepareWord($this->builder->query),
                                    'default_operator' => 'AND',
                                    'analyze_wildcard' => true,
                                    'fields' => ["code^5", "title^2"]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareWord($string): string
    {
        $replacements = [
            '-' => '_',
            '/' => '|u005C|',
            '+' => '\+',
            '=' => '\=',
            '&' => '\&',
            '|' => '\|',
            '>' => '',
            '<' => '',
            '!' => '\!',
            '(' => '\(',
            ')' => '\)',
            '{' => '\{',
            '}' => '\}',
            '[' => '\[',
            ']' => '\]',
            '^' => '\^',
            '"' => '\"',
            '~' => '\~',
            '*' => '\*',
            '?' => '\?',
            ':' => '\:',
            '\\' => '\\\\',
            '`' => '',
            'ʼ' => '',
            "'" => '',
            '‛' => '',
        ];

        $string = strtolower($string);
        return strtr($string, $replacements);
    }
}
