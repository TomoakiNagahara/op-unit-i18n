i18n UNIT
===

## Usage

### Config

```php
// This is default settings, Can change.
Env::Set('i18n',[
  'service'    => 'google',
  'locale-to'  => 'ja:jp',
  'locale-from'=> 'en:us',
  'database'   => [
    'prod'     => 'mysql',
    'host'     => 'localhost',
    'port'     => '3306',
    'user'     => 'i18n',
    'password' => 'password',
    'charset'  => 'utf8',
    'database' => 'onepiece',
  ],
]);
```

### Instantiate

```php
//  Instantiate of i18n object.
if(!$i18n = Unit::Instantiate('i18n') ){
    return;
}
```

### Selftest

```php
//  Selftest
echo $i18n->Selftest()->Auto();
```

### Translation

```php
//  Translation
echo $i18n->Translate('This is test.');
```

### Options

```php
//  Locale of translation source string.
$i18n->LocaleFrom('en:us');

//  Translation target string locale.
$i18n->LocaleTo('ja:jp');
```

## Has JavaScript interface

 Automatically loaded and execute.
 Set the attribute to the tag of the word or sentence you want to translate.

```html
<p data-i18n="true" data-locale="en:US">This is translation test.</p>
```

### Delayed execution

 You can also do this explicitly later.

```js
$OP.i18n.Translate();
```

