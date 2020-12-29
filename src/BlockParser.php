<?php

namespace App;


use DOMElement;
use DOMXPath;
use Error;

class BlockParser extends ContaineredParser
{

    public function __construct($name, $xpathString)
    {
        parent::__construct($name);
        $this->xpathString = $xpathString;
    }


    public function process($context = null)
    {

        if ($context instanceof DOMElement) {
            $domNode = $context->cloneNode(true);
            $context = new DOMXPath($context->ownerDocument);
        }

        $blockNodeList = $context->query($this->xpathString, $domNode ?? null);

        $isXpathCorrupted = $blockNodeList === false;
        if ($isXpathCorrupted) {
            throw new Error("Выражение xpath не дало результата. Либо с ошибкой. $this->xpathString");
        }

        foreach ($blockNodeList as $key => $blockNode) {
            foreach ($this->childs as $child) {
                $parsedData[$key][$child->name] = $child->process($blockNode);
            }
        }


        return $parsedData;
    }
}