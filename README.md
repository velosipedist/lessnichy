# lessnichy

Secluded place where LESS web sources metamorphosed to css


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
