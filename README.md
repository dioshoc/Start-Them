# Тема для натяжки

Пока не все с ней ясно, но в будущем может быть будет

## Для особо одоренных

ТЕМУ НАДО УСТАНОВИТЬ В ПАПКУ С ТЕМАМИ, ТАК КАК ЭТО ТЕМА (и такие проблемы возникали)

Скрипты без ***node.js*** не работают, [тык сюды](https://nodejs.org/ru/) что бы скачать. <br>
Ну и ***git*** не мешало бы поставить [кхэ кхэ](https://git-scm.com/downloads)

# Скрипты

### `npm i`

Инициализация пакетов
<hr>

### `npm audit fix`

Возможно при установке возникнут ошибки, и как бы это команда должна ввсе починить (***не факт***)

*Скорее всего при установке будет две ошибки (когда нибудь-выйдет фикс, ток что может уже и нет)*
<hr>

### `gulp`

Нужен для старта проекта. Преобразование изображений в webp, конвертация скриптов, и тд
<hr>

### `gulp build`

Комманда нужна для завершения проекта, в данный момент у команды осталась лишь одна полезная функция, и это преобразование тега ***img*** в ***picture***, для использования фармата webp изображениями.

ВАЖНО!!!! После завершения работы над файлом, или в данный момент надо прожать ***gulp build***, и отформатировать код удобным вам способом, тег ***img*** изменяться на конструкцию указаную ниже, если эта конструкция будет в одну строку, то при повторном прожатии оно снова приобразит тег ***img***, из за чего получиться конструкция в конструкции, следовательно будет говнокодить. Или просто прожать эту команду перед сдачей проекта.

    <picture><source srcset="favicon.webp" type="image/webp"><img src="favicon.png" alt=""></picture>


# Начало работы

Находим проект который мы хотим поставить, заходим в тему и в консоле выполняем комманду. 

> git clone http://github.com/dioshoc/Start-Them.git

После завршение установки, в консоли прописываем: 

>cd start-them

Что бы прейти в паку с темой.

Следом выполняем команду.

>npm i

Команда которая установит все необходимое для старта.

<hr>

Если в консоли покажет наличие ошибок:

>found 2 vulnerabilities

То выполняем команду

>npm audit fix

<hr>

Дальше заходим в файл **gulpfile.js**, находим там эти строчки:

    function browsersync() {
        browserSync.init({
            proxy: "http://test.local",
            host: "test.local", 
            open: "external",
        }) 
    }

Засеняем "***proxy***" на url нашего сервера (скорее всего локального) <br>
Засеняем "***host***" на название нашего сервера (скорее всего локального)

Для начала работы пишем команду.

    gulp

И наслождаемся работой.

# Работа с темой

## Предисловие

В теме уже установлены и настроены ***bootstrap***, ***jQuery*** и прочие полезные и не очень штуки, если вам вдруг оно не надо, заходим в ***function.php***, и планомерно удаляем. И в идеале найти себе код на установку этих библеотек черз ***npm***, и ввести его заменив слово **install** на **uninstall**

## Основное

Все изменения скриптов и стилей надо делать в папке ***APP*** так как в дальнейшем файлы от туда изменятся и перенесуться уже в основную папку.

## Работа со стилями
Для работы с **Css**, используется препроцессор **SASS**, находиться он в соотеветствующей папке.

Перед начало работы, ВАЖНО!!!!! зайти в ***_config.sass*** и настроить файл под себя. И в идеале, там же и оставлять все перменные которые вы захотите использовать, так как от туда они будут доступны по всему документу, и не будет путаницы.

Стили будут конвертированны в основной документ "*style.css*" в минифицированном виде.

Если для работы вдруг понадабиться что то изменить именно в них, можно будет изменить стиль отображения на нормальный.
Для этого надо зайти в файл ***gulpfile.js***, найти следующую строчки, и раскомментировать ***format: 'beautify'***:

    function styles 
    ...
    .pipe(cleancss({ level: { 1: { specialComments: 0 } }, /* format: 'beautify' */ }))

## Работа со скриптами

Для работы со скриптами можно использовать ***jQuery*** со старта, так как я хоть и без него спокойно живу, много кому он нужен.

В уже выгруженной папке **js**, есть файл ***sart-script.js***, там есть некоторые фишки которые помогают жить, но оно может вам мешать, ток что если что вы предупреждены.

Так же в **function/script.php** есть полезные штуки, но они тоже могут вам мешать.

Если же вдруг вам мало одного файла с js, то смело создавайте второй, но незабдуте его подключить в ***function.php***, делаеться это дублированием следующей строки с заменой имени файла:

    wp_enqueue_script('them-main', get_template_directory_uri() . '/js/main.js', array(), _S_VERSION, true);

## Работа с изображениями 
### Пути изображений
Все мы ленивые люди, и по этому изночально для тега ***img*** прописан путь сразу к папке с изображниями, ток что если у вас в нутри нет никаких дополнительных папок, то можно просто писать название изображения, и вуаля видеть сразу результат.

К сожаленью, это не работает с фонами :(

### Коневертация изображений
Исходные езображение ложить в папку по пути **app/img/**, все изображения из этой папки будут пережаты и конвертированны в webp. Конечные изображения брать по пути **images/**.<br>

ВАЖНО!!! При работе надо указывать правильный путь ***src***, в случае если по пути картинки не будет, конвертер будет бить ошибку

Все теги **img**, будут преобразованны при выполнении комманды ***gulp build***. Им будет присвоина другая структура которая поможет при работе с webp.

ВАЖНО!!! После завершения работы над файлом, или в данный момент надо прожать ***gulp build***, и отформатировать код удобным вам способом, тег ***img*** изменяться на конструкцию указаную ниже, если эта конструкция будет в одну строку, то при повторном прожатии оно снова приобразит тег ***img***, из за чего получиться конструкция в конструкции, следовательно будет говнокодить. Или просто прожать эту команду перед сдачей проекта.

    <picture><source srcset="favicon.webp" type="image/webp"><img src="favicon.png" alt=""></picture>

### Работа с миниатюрами
По умолчанию удалены все виды миниатюр, так как зачастую они мешают, и просто создают кучу мусора. Код для их чистки находиться в ***function.php***. Ток что если вам вдруг они понадобяться, то можите удалить код который убирает нужную вам миниатюру.

Если же вдруг вам понадобиться своя версия миниатюры. То создать ее можно следующим кодом.

    add_action('after_setup_theme', 'more_post_capabilities');
        add_image_size('name', width, height, crop);
    }
### Работа с **alt**
Все теги **img**, будут преобразованны при выполнении комманды **gulp**. Им будет присвоин заполнитель в **alt**, если вы его не укажите его при создании тега **img**. Что бы заменить Стандартное заполнение **alt** нужно перейти в файл **footer.php**, найти следующие строчки:

    if (imgLength[i].getAttribute("alt") === "") {
        imgLength[i].setAttribute('alt', 'Лучше чем ничего') }

И замнить "***Лучше чем ничего***", на свой заполнитель. (Есть две версии этих строчек для просто изображений, и для отформотированных, не забывайте это сделать и там и там)

Так же изночально в проекте стоит ленивая загрузка картинок;)

## Работа с шрифтами
Для подключения шрифтов заходим на сайт [шрифтов гугла](https://fonts.google.com/), находим нужный нам шрифт и копируем ссылку на его подключение, затем заходим в ***function.php***, находим следующие строчки, и изменяем строчку с сылкой.

    // Подключение шрифтов
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', [], _S_VERSION);

Или же это можно сделать в ***_config.sass***, там есть миксин, ток что проблем это вызвать недолжно.

## Работа с functions.php

Почти все функции на данный момент вынесены в отдальные файлы, в папке ***function***.

Большенство всего там подписано, если вам что то не надо, или наоборот надо, то смело правим нужные строчки кода, там так же есть пример для большенства действий которые вам могут понадобиться.

Единственное что стоит упомянуть это следующие строчки кода, если произошли какие то изменения в function.php уже после запуска проекта, то увиличивайте по чуть чуть версию, это надо для отчистки кэша php функций.

    if (!defined('_S_VERSION')) {
        // Replace the version number of the theme on each release.
        define('_S_VERSION', '1.0.0.0');
    }