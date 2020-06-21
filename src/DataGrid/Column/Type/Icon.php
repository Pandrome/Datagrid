<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Icon implements IType
{
    public static function render(Column $column, array $data): array
    {
        $thumb = '';
        $image = $column->options['url'];

        $fileName = '';
        preg_match_all('/{(.*?)}/', $column->options['file'], $matches);
        foreach ($matches[1] as $match) {
            $matchData = $data;
            $matchParts = explode('.', $match);

            if (sizeof($matchParts) > 1 && !empty($data[$matchParts[0]]) && is_array($data[$matchParts[0]])) {
                $matchData = $data[$matchParts[0]];
                $match = $matchParts[1];
            }

            if (isset($matchData[$match])) {
                $fileName = str_replace('{' . $match . '}', $matchData[$match], $column->options['file']);
            }
        }

        if (strpos($fileName, '__image') !== false) {
            if (isset($column->options['thumb']) && strlen($column->options['thumb']) > 0) {
                $thumb = $image . str_replace('{__image}', $column->options['thumb'], $fileName);
            }
            $image .= str_replace('{__image}', $column->options['image'], $fileName);
        }

        if (!file_exists(public_path($image))) {
            $image = '';
        }

        if (!file_exists(public_path($thumb))) {
            $thumb = '';
        }

        return [
            'column' => $column->column,
            'type' => $column->type,
            'thumb' => DIRECTORY_SEPARATOR . $thumb,
            'image' => DIRECTORY_SEPARATOR . $image,
        ];
    }
}
