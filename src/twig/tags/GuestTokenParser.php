<?php

namespace app\twig\tags;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;

/**
 * Represent content if visitor is guest
 *
 *  {% guest %}
 *    content here...
 *  {% endguest %}
 *
 * @internal
 */
class GuestTokenParser extends AbstractTokenParser
{


    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideGuestEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new GuestNode($body, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'guest';
    }

    public function decideGuestEnd(Token $token): bool
    {
        return $token->test('endguest');
    }
}
