### зачем нужен setup_options?
setup_options нужен что бы дать пользователю возможность влиять на ход установки компонента
### Как работает setup_options ?
`setup_options` - это фрагмент PHP кода похожий на `resolver` возвращающий HTML фрагмента с полями формы,
данные из формы передаются в `resolver` в массиве `options`. Например:
в `setup_options` 
```html
<input type="text" name="input_name" value="Привет мир">
```
в `resolver`
```php
$options['input_name'] == "Привет мир"
```
### особенности работы
 - не адекватно работают JS-скрипты, единственный способ нормально использовать js это писать код в event-атрибутах Например:
```html
<input type="text" onclick="console.log(this.value)" name="input_name" value="Привет мир">
```

[полный список окружения](https://github.com/Traineratwot/EasyPack/wiki/setup_options---полный-список-окружения) _(может отличаться от вашего из-за других компонентов)_
