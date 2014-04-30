# lessnichy

Can include  .less stylesheets into HTML template instead of common css manager's output (even if has printed).

For now supports only plain php templates (works in Bitrix, Wordpress etc).
Twig, Yii, Laravel, etc will be supported in nearest future using additional bridges.

## Workflow

Draft for now:

* Requesting `www.yourdomain.com/{lessnichy-dir}` from browser for log in to Lessnichy site-wide session.
* After session opened, small helper toolbar will be sticked at browser window in any site page.
* Edit & upload your LESS files, they will be compiled & saved automatically

### Browser toolbar features

Planned the following:

* notify about changed LESS files
* re-compile page stylesheets by demand
* turn less.watchMode on & off
* log you out from session

### Where to put it and how to use

```yaml
# app directories structure
/webroot
  /app-can-have-any-files
    ...
    template-somewhere.php # use LESS::connect() in <head>, calls server
  /any-folder
  /lessnichy-custom-dir # must be web accessible
    index.php # lessnichy sources entry point, use LESS::listen()
    lessnichy.phar # include this in index.php before listen
    .htaccess # copy from /example dir, to catch browser yoddles, will be auto-bundled in future

```

```yaml
# config.yaml or json, for future versions
minify: true
gzip: false
enable_less: true # for LESS developing sessions
#etc
```

## Requirements

- php 5.3+
- jquery on client side
- no node.js, additional libs, even composer!

## Building phar

Clone repo on your dev machine with git & phing installed run

```
phing build
```

Wait about 10 seconds. Voila!