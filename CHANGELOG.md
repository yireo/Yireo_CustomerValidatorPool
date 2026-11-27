# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 27 November 2026
### Added
- Pool of customer validators, configurable via DI, that runs on every customer save
- Sample validator enforcing a minimum length of the customer first name
- Unit and integration tests

### Fixed
- Validators no longer block password reset, account confirmation and password hash upgrades, which locked out customers with legacy data
- Validator message no longer echoes the (unescaped) first name back to the frontend; multibyte names are counted correctly and a missing first name no longer throws a TypeError
- Pool items not implementing CustomerValidatorInterface are rejected
- Missing Magento_Customer dependency added and license aligned with LICENSE.txt
- README example validator corrected and validator scope documented

### Changed
- PHP 8.1 or higher is required
