<?php

namespace Pandrome\Datagrid\DataGrid\Filter\Type;

use Pandrome\Datagrid\DataGrid\Column;
use Pandrome\Datagrid\DataGrid\Filter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class DateTime extends AType
{
    protected static $operator = 'between';

    protected static function useRelation(EloquentBuilder $query, Column $column, Filter $filter)
    {
        $columnName = $filter->column();
        $values = static::fromAndTillFromValue($filter->value());
        if (!empty($values['from']) || !empty($values['till'])) {
            $query->whereHas($column->relation, function($query) use ($columnName, $values) {
                if (!empty($values['from']) && !empty($values['till'])) {
                    return $query->whereBetween($columnName, [ $values['from'], $values['till'] ]);
                } else if (!empty($values['from'])) {
                    return $query->where($columnName, '>=', $values['from']);
                }

                return $query->where($columnName, '<=', $values['till']);
            });
        }
    }

    protected static function useDirect(EloquentBuilder $query, Column $column, Filter $filter)
    {
        $values = static::fromAndTillFromValue($filter->value());

        if (!empty($values['from']) && !empty($values['till'])) {
            return $query->whereBetween($filter->column(), [$values['from'], $values['till'] ]);
        } else if (!empty($values['from'])) {
            return $query->where($filter->column(), '>=', $values['from']);
        } else if (!empty($values['till'])) {
            return $query->where($filter->column(), '<=', $values['till']);
        }
    }

    protected static function fromAndTillFromValue($values)
    {
        if (!is_array($values)) {
            if (empty($values)) {
                return [null, null];
            }

            if (stripos($values, ' to ') !== false) {
                $valuesSplit = explode(' to ', $values);
                $values = [
                    'from' => (string)$valuesSplit[0] ?? null,
                    'till' => (string)$valuesSplit[1] ?? null
                ];
            } else {
                $values = ['from' => (string)$values];
            }
        } else {
            if (!isset($values['from']) && !isset($values['till'])) {
                $values = array_values($values);
                $tmpValues = ['from' => !empty(current($values)) ? current($values) : null];
                if (!empty(next($values))) {
                    $tmpValues['till'] = current($values);
                }
                $values = $tmpValues;
            }
        }

        if (!empty($values['from'])) {
            $values['from'] = Carbon::createFromDate($values['from']);
        }
        if (!empty($values['till'])) {
            $values['till'] = Carbon::createFromDate($values['till']);
        }

        if (!empty($values['from']) && !empty($values['till'])) {
            if ($values['from']->greaterThan($values['till'])) {
                $till = $values['from'];
                $values['from'] = $values['till'];
                $values['till'] = $till;
                $values['till'] = $values['till']->endofDay();
            }
        }
        if (!empty($values['from'])) {
            $values['from'] = $values['from']->startOfDay();
        }

        if (!empty($values['till'])) {
            $values['till'] = $values['till']->endofDay();
        }

        return $values;
    }
}
