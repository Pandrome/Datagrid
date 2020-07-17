<?php

namespace Pandrome\Datagrid\DataGrid\Column\Type;

use Pandrome\Datagrid\DataGrid\Column;

class Button implements IType
{
    public static function render(Column $column, array $data): array
    {
        $buttonGroup = !empty($column->options['btnGroup']) ? (bool)$column->options['btnGroup'] : false;
        $buttons = $column->options['buttons'];
        foreach ($buttons as $index => &$button) {
            if (!empty($button['conditionalButtons'])) {
                $conditionalButton = static::processConditional($button['conditionalButtons'], $data);
                if (empty($conditionalButton)) {
                    unset($buttons[$index]);
                    continue;
                }
                $button = $conditionalButton;
            }
            static::replaceValues($button, $data);
        }

        return [
            'column' => $column->column,
            'buttons' => array_values($buttons),
            'buttonGroup' => $buttonGroup,
            'type' => $column->type,
            'value' => '',
        ];
    }

    protected static function processConditional(array $buttons, array $data): array
    {
        if (static::validateCondition($buttons['if']['condition'], $data)) {
            unset($buttons['if']['condition']);
            static::replaceValues($buttons['if'], $data);
            return $buttons['if'];
        }

        if (!empty($buttons['elseif'])) {
            foreach ($buttons['elseif'] as $button) {
                if (static::validateCondition($button['condition'], $data)) {
                    unset($button['condition']);
                    static::replaceValues($button, $data);
                    return $button;
                }
            }
        }

        if (!empty($buttons['else'])) {
            static::replaceValues($buttons['else'], $data);
            return $buttons['else'];
        }

        return [];
    }

    protected static function validateCondition(string $condition, array $data): bool
    {
        $recItArray = Column::recursiveIteratorArray($data);
        $condition = [$condition];
        static::replaceValues($condition, $recItArray, true);

        $condition = (string)$condition[0];

        return eval("return " . $condition . " ? true : false;");
    }

    protected static function replaceValues(array &$button, array $data, $useQuotes = false): void
    {
        foreach ($button as &$field) {
            if (!is_string($field)) {
                continue;
            }
            preg_match_all('/{(.*?)}/', $field, $matches);
            foreach ($matches[1] as $match) {
                $matchData = $data;
                $matchParts = explode('.', $match);

                if (sizeof($matchParts) > 1 && !empty($data[$matchParts[0]]) && is_array($data[$matchParts[0]])) {
                    $matchData = $data[$matchParts[0]];
                    $match = $matchParts[1];
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
