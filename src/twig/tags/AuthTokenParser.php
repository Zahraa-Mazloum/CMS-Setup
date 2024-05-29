<?php

namespace app\twig\tags;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;

/**
 * Represent content if visitor is authenticated
 *
 *  {% auth %}
 *    content here...
 *  {% endauth %}
 *
 * @internal
 */
class AuthTokenParser extends AbstractTokenParser
{


    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideAuthEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new AuthNode($body, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'auth';
    }

    public function decideAuthEnd(Token $token): bool
    {
        return $token->test('endauth');
    }
}
