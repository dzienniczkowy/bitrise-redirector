# Bitrise Redirector

[![Latest Stable Version](https://poser.pugx.org/wulkanowy/bitrise-redirector/version?format=flat-square)](https://packagist.org/packages/wulkanowy/bitrise-redirector)
[![Dependency Status](https://www.versioneye.com/user/projects/59aaf1f10fb24f004e2b8610/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59aaf1f10fb24f004e2b8610)
[![StyleCI](https://styleci.io/repos/102099433/shield?branch=master)](https://styleci.io/repos/102099433)

Extends the [Bitrise REST API](http://devcenter.bitrise.io/api/v0.1/) with deterministic/bookmarkable URLs for:

 * latest build on a specific branch
 * build artifact download links

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/wulkanowy/bitrise-redirector)

## Public instance

A public instance of bitrise-redirector is running here:

 * https://bitrise-redirector.herokuapp.com

## URL Patterns

### API v0.1

Get redirected to the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest)):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest`

Get json list of build artifacts for the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest/artifacts)):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest/artifacts`

Get redirected to the download link of a specific build artifact ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest/artifacts/app-debug.apk)):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest/artifacts/{artifact}`


## Development

Installation:

```bash
$ composer create-project wulkanowy/bitrise-redirector
```

Start server with API_KEY and DEBUG variable:

```bash
$ DEBUG=true API_KEY={key} php -S localhost:8080 -t public public/index.php
```

Open the webapp in browser:

http://localhost:8080
