# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Changed
- Simplified by adding a Delegatee that handles most of the complicated stuff
- Enums no longer need to extend macrominds\enum\Enum (this is not even supported anymore)
- provide meaningful Exception when custom Enum is implemented incorrectly.

### Added
- Annotated example classes for ide support
- README section about ide support for code completion
- static methods all() for all enum instances, values() for all values of this enum, names() for all names of this enum
- instance method name()
- fromValue method with strict mode and non-strict mode

### Fixed
- uninitialized enums in calls for ::all(), ::values(), ::names()
- Typo in README

## [0.1.0] – 2016-09-09
### Added
- abstract class Enum with unit tests
- trait Enumerations
- usage guide

### Changed
- removed php 5.6 compatibility, because the test would fail. Need to check on this later.

### Fixed

