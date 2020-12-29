<?php

namespace App;


use DOMElement;
use DOMXPath;
use Error;

class TextParser extends Parser
{

    public function __construct(string $name, string $xpathString)
    {
        parent::__construct($name);
        $this->xpathString = $xpathString;
    }

    public function process($context = null):string
    {

        if ($context instanceof DOMElement) {
            $domNode = $context->cloneNode(true);
            $context = new DOMXPath($context->ownerDocument);
        }

        $DOMNodeList = $context->query($this->xpathString, $domNode ?? null);

        $isXpathCorrupted = $DOMNodeList === false;

        if ($isXpathCorrupted) {
            throw new Error("Выражение xpath не дало результата. Либо с ошибкой.");
        }

        foreach ($DOMNodeList as $node) {
            $buffer[] = $this->full_trim($node->textContent);
        }


        return implode(' ', $buffer);
    }

    private function full_trim($str): string
    {
        return trim(preg_replace('/\s{2,}/', ' ', $str));

    }
}