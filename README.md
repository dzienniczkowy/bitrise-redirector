# Bitrise Redirector

Extends the Bitrise REST API with bookmarkable URLs for latest build and build artifacts

Extends the [Bitrise REST API](http://devcenter.bitrise.io/api/v0.1/) with deterministic/bookmarkable URLs for:

 * latest build on a specific branch
 * build artifact download links

## Public instance

A public instance of bitrise-redirector is running here:

 * https://bitrise-redirector.herokuapp.com

## URL Patterns

### API v0.1

Get redirected to the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest)):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest`

Get json list of build artifacts for the latest build on a specific branch ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest/artifacts)):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest/artifacts`

Get redirected to the download link of a specific build artifact ([example](https://bitrise-redirector.herokuapp.com/api/v0.1/apps/daeff1893f3c8128/master/builds/latest/artifacts/app-debug.apk):

 * `GET /api/v0.1/apps/{slug}/{branch}/builds/latest/artifacts/{artifact}`


## Development

Install composer dependencies:

```bash
$ composer install
```
