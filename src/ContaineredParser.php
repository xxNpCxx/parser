<?php

namespace App;


use SplObjectStorage;

abstract class ContaineredParser extends Parser
{
    protected SplObjectStorage $childs;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->childs = new SplObjectStorage();
    }

    public function add(Parser $parser): void
    {
        $this->childs->attach($parser);
    }

    public function remove(Parser $parser): void
    {
        $this->childs->detach($parser);
    }
}