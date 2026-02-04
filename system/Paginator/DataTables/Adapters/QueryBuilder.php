<?php

namespace Core\Paginator\DataTables\Adapters;

use Phalcon\Paginator\Adapter\QueryBuilder as PQueryBuilder;
use Phalcon\Mvc\Model\Query\Builder as QBuilder;

class QueryBuilder extends AbstractAdapter
{
    /** @var QBuilder */
    protected $builder;

    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    public function getResponse()
    {
        // Gunakan method normal Phalcon tanpa CAST function
        // karena CAST tidak didukung dalam PHQL


        $builder = new PQueryBuilder([
            'builder' => $this->builder,
            'limit'   => 1,
            'page'    => 1,
        ]);

        // Tampilkan raw SQL query yang dihasilkan builder

        $total = $builder->paginate();

        $this->bindGlobalSearch();

        $this->bind('column_search', function ($column, $search) {
            $expr = $this->resolveColumnExpression($column);
            if (!$expr || strpos($expr, '*') !== false) return;
            $param = $this->buildParamKey($expr);
            $this->builder->andWhere("{$expr} LIKE :{$param}:", [$param => "%{$search}%"]);
        });

        $this->bind('order', function ($order) {
            if (!empty($order)) {
                $this->builder->orderBy(implode(', ', $order));
            }
        });

        $builder = new PQueryBuilder([
            'builder' => $this->builder,
            'limit'   => $this->parser->getLimit(),
            'page'    => $this->parser->getPage(),
        ]);

        $filtered = $builder->paginate();

        return $this->formResponse([
            'total'    => $total->total_items,
            'filtered' => $filtered->total_items,
            'data'     => $filtered->items->toArray(),
        ]);
    }

        // Method-method untuk handle CAST function sudah dihapus
    // karena tidak sesuai standar Phalcon dan menyebabkan error

    // Method-method untuk handle CAST function sudah dihapus
    // karena tidak sesuai standar Phalcon dan menyebabkan error

    private function bindGlobalSearch()
    {
        $search = $this->parser->getSearchValue();
        if (!mb_strlen($search)) return;

        $orLikes = [];
        $binds = [];
        $search = $this->sanitize($search);
        foreach ($this->parser->getSearchableColumns() as $column) {
            $expr = $this->resolveColumnExpression($column);
            if (!$expr || strpos($expr, '*') !== false) continue;
            $param = $this->buildParamKey($expr);
            $orLikes[] = "{$expr} LIKE :{$param}:";
            $binds[$param] = "%{$search}%";
        }

        if (empty($orLikes)) {
            return;
        }

        $this->builder->andWhere('(' . join(' OR ', $orLikes) . ')', $binds);
    }

    private function resolveColumnExpression($column)
    {
        foreach ($this->columns as $col) {
            $parts = preg_split('/\s+AS\s+/i', $col);
            if (count($parts) === 2) {
                $expr = trim($parts[0]);
                $alias = trim($parts[1]);
                if ($alias === $column) {
                    return $expr;
                }
            }
        }
        if (strpos($column, '.') !== false) {
            return $column;
        }
        $main = $this->getMainAlias();
        return $main ? ($main . '.' . $column) : $column;
    }

    private function buildParamKey($expr)
    {
        return 'key_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $expr);
    }

    private function getMainAlias()
    {
        if (!isset($this->builder)) return null;
        if (method_exists($this->builder, 'getFrom')) {
            $from = $this->builder->getFrom();
            if (is_array($from)) {
                $keys = array_keys($from);
                return isset($keys[0]) ? $keys[0] : null;
            }
        }
        return null;
    }
}
