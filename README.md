# Bitrise Redirector

[![CircleCI](https://img.shields.io/circleci/project/github/wulkanowy/bitrise-redirector.svg?style=flat-square)](https://circleci.com/gh/wulkanowy/bitrise-redirector)
[![Codecov](https://img.shields.io/codecov/c/github/wulkanowy/bitrise-redirector/master.svg?style=flat-square)](https://codecov.io/gh/wulkanowy/bitrise-redirector)
[![StyleCI](https://styleci.io/repos/102099433/shield?branch=master)](https://styleci.io/repos/102099433)
[![Latest Stable Version](https://poser.pugx.org/wulkanowy/bitrise-redirector/version?format=flat-square)](https://packagist.org/packages/wulkanowy/bitrise-redirector)
[![Total Downloads](https://poser.pugx.org/wulkanowy/bitrise-redirector/downloads?format=flat-square)](https://packagist.org/packages/wulkanowy/bitrise-redirector)
[![BCH compliance](https://bettercodehub.com/edge/badge/wulkanowy/bitrise-redirector?branch=master)](https://bettercodehub.com/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wulkanowy/bitrise-redirector/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wulkanowy/bitrise-redirector/?branch=master)

Extends the [Bitrise REST API](http://devcenter.bitrise.io/api/v0.1/) with deterministic/bookmarkable URLs for:

 * latest build on a specific branch
 * build artifact download links

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/wulkanowy/bitrise-redirector)

## Public instance

A public instance of bitrise-redirector is running here:

 * https://bitrise-redirector.herokuapp.com

## URL Patterns

### API v0.1

Get redirected to the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master)):

 * `GET /v0.1/apps/{slug}/builds/{branch}`

Get json list of build artifacts for the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master/artifacts)):

 * `GET /v0.1/apps/{slug}/builds/{branch}/artifacts`

Get redirected to the download link of a specific build artifact:

 * `GET /v0.1/apps/{slug}/builds/{branch}/artifacts/{filename}` - [example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master/artifacts/app-debug-bitrise-signed.apk)
 * `GET /v0.1/apps/{slug}/builds/{branch}/artifacts/{index}` - [example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master/artifacts/0)

Get info of last artifact on specific branch:

 * `GET /v0.1/apps/{slug}/builds/{branch}/artifacts/{filename}/info` - [example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master/artifacts/app-debug-bitrise-signed.apk/info)
 * `GET /v0.1/apps/{slug}/builds/{branch}/artifacts/{index}/info` [example](https://bitrise-redirector.herokuapp.com/v0.1/apps/daeff1893f3c8128/builds/master/artifacts/0/info)

## Development

Installation:

```bash
$ composer create-project wulkanowy/bitrise-redirector
```

Start server with API_KEY and DEBUG variable:

```bash
$ bin/console server:run
```

Open the webapp in browser:

http://127.0.0.1:8000
