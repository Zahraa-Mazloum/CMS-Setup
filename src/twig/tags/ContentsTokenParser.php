<?php

namespace app\twig\tags;

use Exception;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;

class ContentsTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();

        $keys = ["type", "limit", "sort", "category"];

        $options = [];

        $i = 0;
        $key = "";

        while ($value = $i % 3 == 2 ? $this->parser->getExpressionParser()->parseExpression() : $stream->next()->getValue()) {
            switch ($i % 3) {
                case 0:
                    $value = strtolower($value);
                    if (!in_array($value, $keys)) {
                        throw new Exception("`contents` Block Exception : only available attributes are : " . implode(", ", $keys));
                    } else if (array_key_exists($value, $options)) {
                        throw new Exception("`contents` Block Exception : attribute \"$value\" duplicated");
                    }
                    $key = $value;
                    break;
                case 1:
                    if ($value != "=") {
                        throw new Exception("`contents` Block Exception : invalid syntax, format of attribute is name=\"value\"");
                    }
                    break;
                case 2:
                    $options[$key] = $value;
                    break;
            }
            $i++;
        }

        if (!$value && $i % 3 == 2) {
            throw new Exception("`contents` Block Exception : invalid syntax, format of attribute is name=\"value\"");
        }

        $body = $this->parser->subparse([$this, 'decideContentEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);
        return new ContentsNode($body, $options, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'contents';
    }

    public function decideContentEnd(Token $token): bool
    {
        return $token->test('endcontents');
    }
}
