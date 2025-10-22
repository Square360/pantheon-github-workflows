# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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