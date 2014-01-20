# lessnichy

Secluded place where LESS web sources metamorphosed to css.

Also can print out .less stylesheets instead of common css manager's output (even if has printed)


### Draft plan

```yaml
# app directories structure
/webroot
  /app-can-have-any-files
  /any-folder
  /lessnichy
    /js
      lessnichy.js # client-side LESS compilation listener
    index.php # lessnichy sources entry point
    slim.phar # distribute router in compact way
    Lessnichy.php # service facade
    
```

```yaml
# config
minify: true
gzip: false
#etc
```

```php
// index.php

$app->get('/', function(){
  // give hints doc about usage
});

$app->get('/:sourcename.css', function(){
  // gzip css output
});

$app->put('/:sourcename.less', function(){
  // update CSS file content with internal minifier 
  // using for example https://packagist.org/packages/matthiasmullie/minify
});
```

```php
// workflow

// 1. Print trackable .less sources somewhere on php template
Lessnichy::register([
  '/css/main.less',     // webroot will be guessed if path starts from /
  '@webroot/css/add-lib.less',  // maybe paths will be just webroot-related or @tokenized
]);

/* 
  under the hood:
  - bootstrap Lessnichy on first register() call
    - detect absolute url for ajax calls
    - register_shutdown_function() for post-printing LESS.compiled hook with DOM Mutation Observer
  - queue LESS scripts setup:
   - var less = {watch: true, interval: 3000} from add options, must be printed before less.js script
  - queue registered files
    - <link ... rel="stylesheet/less" href="{path-to.less}"/> print task
    - capture mapped <link ... href="{path-to.css}" /> and replace with .less on shutdown if possible
*/

```
