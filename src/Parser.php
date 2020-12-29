<?php

namespace App;

use DOMElement;
use DOMNodeList;
use DOMXPath;
use Error;
use GuzzleHttp\Client;

abstract class Parser
{
    protected ?string $name;
    protected Client $httpClient;
    protected string $xpathString;


    public function __construct($name = null)
    {
        $this->name = $name ?? uniqid();
        $this->httpClient = new Client(['verify' => false]);
    }

    abstract public function process($context = null);

    protected function buildDomContext($context = null)
    {
        if ($context instanceof DOMElement) {
            $domNode = $context->cloneNode(true);
            $context = new DOMXPath($context->ownerDocument);
            $DOMNodeList = $context->query($this->xpathString, $domNode ?? null);

        }else if($context instanceof DOMXPath){
            $DOMNodeList = $context->query($this->xpathString);
        }else{
            $DOMNodeList = $context->query($this->xpathString, $context);
        }

        $isXpathCorrupted = ($DOMNodeList == false);

        if ($isXpathCorrupted) {
            throw new Error("Выражение xpath не дало результата. Либо с ошибкой. $this->xpathString");
        }

        return $DOMNodeList;
    }
}