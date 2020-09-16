Компонент для автоматического создания компонентов и транспортных пакетов
 ---
позволяет в 1 клик создать структуру компонента, создать xml схему таблиц, и упаковать компонент в транспортный пакет

## Использование
 1) создать пакет, для этого
    - нажать кнопку `Создать новы пакет` 
    - в появившемся окне достаточно заполнить поля  `Название` и `Версия`
      - что бы изменить/добавить данные, `пкм` по созданному пакету  выбрать пункт `Обновить`
    * за тем `пкм` по созданному пакету, выбрать пункт `Создать структуру`
      - здесь можно выбрать все, либо то что вам нужно
    - за тем `пкм` по созданному пакету, выбрать пункт `Создать пакет`
      - через несколько секунд будет создан пакет с вашим расширением в папке `core/packages` и ярлык на него в папке `/EasypackExtras` а на имени вашего появиться ссылка для скачивания на последню версию пакета
 2) что означает каждое поле в `Создать структуру`
    - создать папку {папка} в {путь}? - создает папку.
    - создать xml схемы таблиц и их классы? - создает `Xpdo` схемы ваших таблиц
    - создать пространство имён для {Название} - создает пространство имён
    - Сохранить элементы(плагины,чанки,снипеты,...) в папку /elements/ - сохраняет все элементы modx вашего расширения в папку elements в ядре вашего расширения
 3) что означает каждое поле в `Создать новы пакет`/`Обновить`
    - `Название` - имя вашего расширения на английском, без пробелов, с Заглавными буквами
    - `Версия`   - версия вашего расширения в формате 0.0.0-pl,0.0.0-beta, 0.0.0-alpha
    - `Чанк`,`Сниппет`,`Плагин`,`Шаблон`,`Меню`,`Настройки` - элементы modx вашего расширения
    - `Таблицы` - таблицы в бд вашего расширения например `modx_easypack_extras`
    - `Префикс таблицы` - префикс таблицы (рекомендуется оставить по умолчанию)
    - `Путь к core` - путь к ядру вашего расширения от `MODX_BASE_PATH` если у вас стандартное расположение каталогов оставьте как есть
    - `Путь к assets` - путь к открытому каталогу вашего расширения от `MODX_BASE_PATH` если у вас стандартное расположение каталогов оставьте как есть  
    - `Зависимости` - json с зависимостями (для опытных пользователей)
    - `Readme` - путь к readme файлу, по умолчанию находиться в папке docs вашего расширения
    - `История изменений`- путь к changelog файлу, по умолчанию находиться в папке docs вашего расширения
    - `путь к файлу setup_option` - (для опытных пользователей)
    - `путь к файлу php_resolver` - исполняемы php файл запускающийся при установке пакета, если оставить пустым создастся автоматически для создания ваших таблиц (для опытных пользователей)
    - `Лицензия` - путь к файлу с лицензией, по умолчанию находиться в папке docs вашего расширения
