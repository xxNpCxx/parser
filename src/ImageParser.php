<?php

namespace App;


use DOMElement;
use DOMNode;
use DOMXPath;
use Error;

class ImageParser extends Parser
{

    public function __construct(string $name, string $xpathString)
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

        $DOMNodeList = $context->query($this->xpathString, $domNode ?? null);

        $isXpathCorrupted = $DOMNodeList === false;

        if ($isXpathCorrupted) {
            throw new Error("Выражение xpath не дало результата. Либо с ошибкой.");
        }

        $parsedData = null;

        foreach ($DOMNodeList as $node) {

            $body = $this->httpClient->get($node->textContent)->getBody();
            $image = imagecreatefromstring($body->read($body->getSize()));
            ob_start();
            imagejpeg($image);
            $image = ob_get_clean();
            $tempfilepath = tempnam(sys_get_temp_dir(),'img');
            file_put_contents($tempfilepath,$image);

            $parsedData[] = $tempfilepath;

        }
        return $parsedData ? implode(PHP_EOL, $parsedData) : [];
    }
}