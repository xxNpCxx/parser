<?php

namespace App;

use DOMDocument;
use DOMXPath;
use Error;

class PageParser extends ContaineredParser
{

    private string $url;

    public function __construct(string $url)
    {
        parent::__construct(null);
        $this->url = $url;
    }

    public function process($context = null)
    {
        $result = $this->httpClient->get($this->url);
        $html = $result->getBody()->getContents();
        $domDocument = new DOMDocument();
        $domDocument->loadHTML($html);

        if (false == $domDocument) {
            throw new Error('Не удалось получить страницу');
        }

        $DomXpath = new DOMXPath($domDocument);
        $parsedData = [];

        foreach ($this->childs as $child) {
            $parsedData[$child->name] = $child->process($DomXpath);
        }

        return $parsedData;
    }
}