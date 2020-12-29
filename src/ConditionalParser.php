<?php

namespace App;


use DOMElement;
use DOMNode;
use DOMXPath;
use Error;
use Exception;

class ConditionalParser extends Parser
{
    private array $conditionalParsers;

    public function __construct(string $name, string $xpath)
    {
        parent::__construct($name);
        $this->xpathString = $xpath;
    }

    /**
     * @throws Exception
     */
    public function process( $context = null): array
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
        $parsedText = $DOMNodeList->item(0)->textContent;

        $nextLevelParser = new PageParser($parsedText);
        foreach ($this->conditionalParsers as $matchedName => $conditionalParser) {
            if (strpos($parsedText, $matchedName)) {
                foreach ($conditionalParser as $p){
                    $nextLevelParser->add($p);
                }
            }
        }
        return $nextLevelParser->process();

    }

    public function add(string $needleStringToHandle, Parser $parser)
    {
        $this->conditionalParsers[$needleStringToHandle][] = $parser;
    }

}