<?php

use App\BlockParser;
use App\ConditionalParser;
use App\ImageParser;
use App\PageParser;
use App\TextParser;

require __DIR__ . '/vendor/autoload.php';

$RBCParser = new PageParser("https://sportrbc.ru/news/5feb46039a7947dfa7873c6e?from=newsfeed");
$block = new BlockParser('news', "//div[@class='js-news-feed-list']/a");
$block->add(new TextParser('short_description', "./span[1]"));
$block->add(new TextParser('date', "./span[2]"));
$conditional = new ConditionalParser('fullnews', "./@href");
$conditional->add('autonews.ru', new TextParser('fulldescription', "(//div[@class='article__text'])[1]/descendant::*[not(contains(@class,'article__related'))]/text() | (//div[@class='article__header__anons'])[1]/text() | (//div[@class='article__text'])[1]/descendant::*[not(contains(@class,'article__related'))]/img"));
$conditional->add('sportrbc.ru', new ImageParser('img', "(//div[contains(@class,'article__main-image')])[1]//img[1]/@src"));
$conditional->add('sportrbc.ru', new TextParser('fulldescription', "(//div[@class='article__text article__text_free'])[1]/descendant::*[not(contains(@class,'news-bar')) and not(contains(@class,'banner')) and not(self::script or self::style) and not(contains(@class,'article__inline-item')) and not(contains(@class,'article__main-image__author'))]/text()"));
$block->add($conditional);
$RBCParser->add($block);
$parsedData = $RBCParser->process();

print_r($parsedData);