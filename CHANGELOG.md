# Release 2.0.14 (2026-05-21)

### Bug Fixes

* repin shared-workflows SHA to v3.2.0 (01ab60d) — picks up `drush deploy` in `pantheon-post-deploy-drush` (replaces separate updb/cim/cr with a single canonical sequence that adds the cache rebuild between updb and cim, and runs deploy:hook for post-config-import data migrations). See [`Square360/shared-workflows` v3.2.0 release notes](https://github.com/Square360/shared-workflows/releases/tag/v3.2.0) for the full behaviour change and the breaking-change note about the removed `[config-first]` PR-title flag.

# CHANGELOG backfill (2026-05-21)

The semantic-release workflow at `.github/workflows/semantic-release.yml` triggers on `push: main`, but this repo's default branch is `master`. As a result, v1.6.0 through v2.0.13 were tagged via the GitHub UI without semantic-release running, and CHANGELOG.md was not auto-updated for those releases. The entries below were reconstructed manually from `git log` and tag metadata.

# Release 2.0.13 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.6 (9de5eb4) — drush retry wrapper for transient classloader failures on freshly-pushed Pantheon environments.

# Release 2.0.12 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.5 (d4e1fe6) — heavier Drupal-bootstrap probe in env-settle wait.

# Release 2.0.11 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.4 (89f2c39) — wait for Pantheon env filesystem to settle before running drush; fixes the propagation race that caused intermittent drush bootstrap failures right after a code push.

# Release 2.0.10 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.3 (09f0b16)

# Release 2.0.9 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.2 (8574e58)

# Release 2.0.8 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.1 (22fe190)

# Release 2.0.7 (2026-05-20)

### Bug Fixes

* repin shared-workflows SHA to v3.1.0 (1188cb3) — adds VRT opt-in on RC multidev; forward CLICKUP_* secrets on RC.

# Release 2.0.6 (2026-05-19)

### Bug Fixes

* repin shared-workflows SHA to v3.0.5 (79a09b3) — align repo refs to the renamed `shared-workflows` (formerly `shared-pantheon-workflows`).

# Release 2.0.5 (2026-05-18)

### Bug Fixes

* combine epic into deploy-multidev; stop double-deploy on epic merges (f779bf3) — consolidates the separate epic-deploy template into deploy-multidev so a merge to an epic branch no longer triggers two deploys.

# Release 2.0.4 (2026-05-18)

### Bug Fixes

* repin shared-workflows SHA to v3.0.4 (b8a1b63)

# Release 2.0.3 (2026-05-18)

### Bug Fixes

* repin shared-workflows SHA to v3.0.3 (aa8c639)

# Release 2.0.2 (2026-05-18)

### Bug Fixes

* repin shared-workflows SHA to v3.0.2 (01165b1)

# Release 2.0.1 (2026-05-18)

### Bug Fixes

* repin shared-workflows SHA to v3.0.1 (66dcddc)

# Release 2.0.0 (2026-05-18)

### ⚠ BREAKING CHANGES

* route consumer templates to shared-workflows v3.0.0 (2615272) — consumer templates now target the new four-workflow shared-workflows v3 layout (`reusable-pantheon-deploy-{dev,pr-multidev,rc-multidev,epic-multidev}.yml`) instead of the prior monolithic `reusable-deploy-pantheon.yml` / `reusable-deploy-multidev.yml`. Consumer sites pick this up automatically on `composer update`.

# Release 1.6.0 (2026-05-13)

### Security

* pin all reusable-workflow uses to immutable SHA (OWASP HIGH) (ac5d906) — replaces floating `@main` pins in consumer templates with immutable commit SHAs to mitigate the OWASP supply-chain risk flagged by `security-scan`.

# Release 1.5.0 (2026-05-13)

### Features

* (): added semantic release for this repo (efc8840)

### Continuous Integration

* (): Add semantic release workflow to this repo (07cc68e)

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2025-10-23

### Added

- Slack notification support for both deployment workflows using Slack Web API
- Optional `slack_channel` and `slack_bot_token` parameters in workflow templates
- Deployment status notifications with environment URLs and troubleshooting links
- Support for posting to any Slack channel using bot token authentication

### Changed

- Switched from webhook-based Slack notifications to bot token approach for more flexibility
- Bot token allows posting to multiple channels without separate webhooks

### Updated

- `deploy-to-dev.yml` template now includes optional Slack notification parameters
- `deploy-multidev.yml` template now includes optional Slack notification parameters
- README documentation updated to describe bot token configuration

## [1.0.1] - 2025-10-22

### Added

- Conditional environment naming logic for multidev deployments based on target branch
- Automatic RC environment naming (`rc-{YEAR}-{WEEK}`) for develop branch merges
- Dynamic target environment calculation job in deploy-multidev template

### Changed

- Updated multidev workflow template to match simplified shared workflow interface
- Removed redundant multidev creation parameters (`create_multidev`, `clone_content`, `delete_multidev_after`)
- Simplified parameter structure to only require `pantheon_site` and `target_env`
- Improved workflow efficiency by leveraging pantheon-systems/push-to-pantheon built-in capabilities
- Enhanced environment naming strategy to support both RC and PR naming conventions

### Fixed

- Resolved conflicts between manual multidev creation and pantheon-systems action automation
- Eliminated redundant workflow steps that were duplicating functionality
- Restored RC environment naming for develop branch deployments that was lost during workflow simplification

## [1.0.0] - 2025-10-22

### Added

- Initial release of Pantheon GitHub Workflows Composer package
- Automatic installation of GitHub Actions workflow files
- `deploy-to-dev.yml` workflow for DEV environment deployments
- `deploy-multidev.yml` workflow for pull request-based multidev deployments
- Composer plugin architecture for automated file management
- Integration with Square360/shared-pantheon-workflows repository
- Aggressive update strategy to ensure consistency across projects
- Template-based workflow generation
- Automatic documentation creation

### Features

- Zero-configuration setup for new projects
- Automatic workflow file updates on `composer update`
- Preservation of custom workflow files with different names
- Comprehensive logging and error handling
- PSR-4 autoloading with proper namespace structure

### Documentation

- Complete README with installation and usage instructions
- Development documentation in workflow-configuration/
- Inline code documentation for all classes and methods
- Template documentation for workflow configuration

### Security

- Secure secret handling in workflow templates
- Proper Pantheon authentication patterns
- GitHub token management for semantic release
