<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Button implements IType
{
    public static function render(Column $column, array $data): array
    {
        foreach ($column->options as &$options) {
            foreach ($options as &$option) {
                preg_match_all('/{(.*?)}/', $option, $matches);
                foreach ($matches[1] as $match) {
                    $matchData = $data;
                    $matchParts = explode('.', $match);

                    if (sizeof($matchParts) > 1 && !empty($data[$matchParts[0]]) && is_array($data[$matchParts[0]])) {
                        $matchData = $data[$matchParts[0]];
                        $match = $matchParts[1];
                    }

                    if (isset($matchData[$match])) {
                        $option = str_replace('{' . $match . '}', $matchData[$match], $option);
                    }
                }
            }
        }

        return [
            'column' => $column->column,
            'buttons' => $column->options,
            'type' => $column->type,
            'value' => '',
        ];
    }
}
