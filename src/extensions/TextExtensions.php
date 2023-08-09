<?php

namespace Humbrain\Framework\extensions;

use Twig\Extension\AbstractExtension;

class TextExtensions extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function excerpt(string $content, int $limit = 100): string
    {
        if (mb_strlen($content) <= $limit) :
            return $content;
        endif;
        $lastSpace = mb_strpos($content, ' ', $limit);
        return mb_substr($content, 0, $lastSpace) . '...';
    }
}
