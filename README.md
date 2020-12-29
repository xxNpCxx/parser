# parser
WEB Парсер основанный на xpath выражениях.

Возможности:
 - Может выбирать стратегию дальнейшего парсинга в зависимости от данных полученных в процессе парсинга url.
 - Может парсить картинки.
 - Может группировать парсеры.

# Установка
clone repository
cd parser
composer install

# Инструкция

Достаточно запустить 1 раз метод process() у родительского парсера, чтобы получить массив данных со всех вложенных парсеров. При этом ключами результирующего массива будут являтся имена вложенных парсеров , сохраняя структуру вложенности.
| Class | Описание |
| ------ | ------ |
| PageParser | Базовый класс. С него начинается парсинг. Указывается ссылка на стартовый ресурс, затем в него добавляются конкретные парсеры |
| BlockParser | Контейнер для парсеров. Используется для группировки парсеров в том числе других блоков. Все дочерние парсеры получают не всю страницу, а контекст. Благодаря этому упрощается xpath выражения вложенных парсеров, и ускоряется процесс парсинга. |
| ConditionalParser | Условный парсер - нужен чтобы погружаться по странице вглубь. При добавлении в него дочерних парсеров, они группируются по имени, и будут применены В случае если в имени и в результате xpath выражения будет совпадение. |
| TextParser | Используется для выборки текста. Не имеет вложенных парсеров. |
| ImageParser | Используется для загрузки изображения во временное хранилище, возвращает путь до файла. |
| Google Analytics | [plugins/googleanalytics/README.md][PlGa] |

# Запуск
```
php index.php
```
# Пример клиентского кода для парсинга rbc новостной ленты со вложенными данными
```php
$RBCParser = new PageParser("https://rbc.ru");
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
```
