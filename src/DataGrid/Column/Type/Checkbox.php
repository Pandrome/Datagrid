<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Checkbox implements IType
{
    public static function render(Column $column, array $data): array
    {
        $options = $column->options;
        static::replaceValues($options, $data);

        return [
            'column' => $column->column,
            'type' => $column->type,
            'value' => $options['value'],
        ];
    }

    protected static function replaceValues(array &$options, array $data, $useQuotes = false): void
    {
        foreach ($options as &$field) {
            if (!is_string($field)) {
                continue;
            }
            preg_match_all('/{([a-z0-9_.]*?)}/', $field, $matches);
            foreach ($matches[1] as $match) {
                $matchData = $data;
                $matchParts = explode('.', $match);

                if (!isset($matchData[$match])) {
                    if (sizeof($matchParts) > 1 && !empty($data[$matchParts[0]]) && is_array($data[$matchParts[0]])) {
                        $matchData = $data[$matchParts[0]];
                        $match = $matchParts[1];
                    }
                }
                if (isset($matchData[$match])) {
                    if ($useQuotes) {
                        $matchData[$match] = "'" . $matchData[$match] . "'";
                    }
                    $field = str_replace('{' . $match . '}', $matchData[$match], $field);
                }
            }
        }
    }
}
