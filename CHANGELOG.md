# Changelog

## x.y.z - UNRELEASED

--------

## 2.0.0 - 2018-04-15

### Added

* [Storageless] Allow a SessionInterface to be used as a PSR-15 middleware.
* [CookieInterface] The Cookie class now implements a CookieInterface.

### Changed

* [SessionInstance] Use the new read_and_close option when starting the session.
* Replaced protected methods/properties with private for safer encapsulation.
* All classes now use type hints where possible.
* [Support] Dropped support for PHP 5.6.
* [Support] Dropped support for PHP 7.0.

--------

## 1.4.0 - 2018-04-02

### Added

* [Session] Added a setInstance() method to set the SessionInstance.
* [SessionInstance] Added a regenerate() method to update the session ID.
* [Support] Added support for PHP 7.2.

--------

## 1.3.0 - 2017-11-11

### Added

* [Session] Added a getInstance() method to get the SessionInstance.

--------

## 1.2.0 - 2017-07-18

### Added

* [SessionInstance] Added the ability to use a specific session ID. (Thanks @subins2000)

### Changed

* [Support] Dropped support for HHVM.

--------

## 1.1.0 - 2017-01-31

### Added

* [Support] Added support for PHP 7.1
* [Cookie] Add a cookie class to managing session cookies.

### Fixed

* [Cookie] Ensure the session cookie is refreshed on each use.

--------

## 1.0.0 - 2015-07-19

### Added

* [SessionInstance] The main session handler class.
* [Session] A static version of the session handler.
* [SessionInterface] An interface to code against.

--------
