<?php

namespace Square360\PantheonWorkflows;

use Composer\Script\Event;

/**
 * Composer scripts for Square360 Pantheon GitHub Workflows package.
 *
 * Handles automated installation of GitHub Actions workflow files
 * when the package is installed or updated via Composer.
 */
class ComposerScripts {

  /**
   * Post-install hook.
   *
   * Runs after composer install to set up workflow files.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event object.
   */
  public static function postInstall(Event $event) {
    static::installFiles($event);
  }

  /**
   * Post-update hook.
   *
   * Runs after composer update to update workflow files.
   * ALWAYS overwrites existing workflow files to ensure latest standards.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event object.
   */
  public static function postUpdate(Event $event) {
    static::installFiles($event);
  }

  /**
   * Install GitHub Actions workflow files to the project.
   *
   * This method:
   * - Creates .github/workflows/ directory
   * - OVERWRITES existing workflow files (deploy-to-dev.yml, deploy-multidev.yml)
   * - Creates CHANGELOG-WORKFLOWS.md from template if it doesn't exist
   * - Preserves any custom workflow files not managed by this package
   *
   * @param \Composer\Script\Event $event
   *   The Composer event object.
   */
  private static function installFiles(Event $event) {
    $io = $event->getIO();
    $composer = $event->getComposer();
    $config = $composer->getConfig();

    // Debug output
    $io->write("\n<info>🚀 Installing Square360 Pantheon GitHub Workflows...</info>");

    // Determine paths
    $vendorDir = $config->get('vendor-dir');
    $packageDir = $vendorDir . '/square360/pantheon-github-workflows';
    $projectRoot = dirname($vendorDir);

    // Check if package directory exists
    if (!is_dir($packageDir)) {
      $io->writeError("<error>❌ Package directory not found: $packageDir</error>");
      return;
    }

    // Create target directory
    $targetDir = $projectRoot . '/.github/workflows';
    if (!is_dir($targetDir)) {
      if (!mkdir($targetDir, 0755, true)) {
        $io->writeError("<error>❌ Could not create directory: $targetDir</error>");
        return;
      }
      $io->write("<info>📁 Created directory: .github/workflows/</info>");
    }

    // Workflow files to install (these will be OVERWRITTEN)
    $workflowFiles = [
      'deploy-to-dev.yml',
      'deploy-multidev.yml',
    ];

    // Copy workflow files from templates
    $templatesDir = $packageDir . '/workflow-configuration/templates';
    foreach ($workflowFiles as $filename) {
      $source = $templatesDir . '/' . $filename;
      $dest = $targetDir . '/' . $filename;

      if (file_exists($source)) {
        if (copy($source, $dest)) {
          $io->write("<info>✅ Installed/Updated: .github/workflows/$filename</info>");
        } else {
          $io->writeError("<error>❌ Failed to copy: $filename</error>");
        }
      } else {
        $io->writeError("<error>❌ Source file not found: $source</error>");
      }
    }

    // Create changelog file (only if it doesn't exist)
    $changelogPath = $projectRoot . '/CHANGELOG-WORKFLOWS.md';
    if (!file_exists($changelogPath)) {
      $changelogContent = static::getDefaultChangelogTemplate();
      if (file_put_contents($changelogPath, $changelogContent)) {
        $io->write("<info>✅ Created: CHANGELOG-WORKFLOWS.md</info>");
      } else {
        $io->writeError("<error>❌ Failed to create: CHANGELOG-WORKFLOWS.md</error>");
      }
    } else {
      $io->write("<comment>⚡ Preserved existing: CHANGELOG-WORKFLOWS.md</comment>");
    }

    // Create README if it doesn't exist
    $readmePath = $targetDir . '/README.md';
    if (!file_exists($readmePath)) {
      $readmeContent = static::getWorkflowReadmeTemplate();
      if (file_put_contents($readmePath, $readmeContent)) {
        $io->write("<info>✅ Created: .github/workflows/README.md</info>");
      }
    } else {
      $io->write("<comment>⚡ Preserved existing: .github/workflows/README.md</comment>");
    }

    $io->write("<info>🎉 Square360 Pantheon workflows installation complete!</info>");
    $io->write("<comment>ℹ️  Workflow files are managed by this package and will be overwritten on updates.</comment>");
    $io->write("<comment>ℹ️  Add custom workflows with different names to avoid conflicts.</comment>");
  }

  /**
   * Get default changelog template content.
   *
   * @return string
   *   The changelog template content.
   */
  private static function getDefaultChangelogTemplate() {
    $date = date('Y-m-d');
    return <<<EOD
# Workflow Changelog

This file tracks changes made to GitHub Actions workflows in this project.

## [Unreleased]

### Added
- Installed Square360 Pantheon GitHub Workflows package

## [$date] - Package Installation

### Added
- `deploy-to-dev.yml` - Deployment to Pantheon DEV environment
- `deploy-multidev.yml` - Pull request deployments to multidev environments
- Automated workflow management via Composer package

### Notes
- Workflow files are managed by `square360/pantheon-github-workflows` package
- Files will be automatically updated when the package is updated
- Custom workflows should use different filenames to avoid conflicts

EOD;
  }

  /**
   * Get workflow README template content.
   *
   * @return string
   *   The README template content.
   */
  private static function getWorkflowReadmeTemplate() {
    return <<<EOD
# GitHub Actions Workflows

This directory contains GitHub Actions workflows for automated deployment to Pantheon.

## Managed Workflows

The following workflows are managed by the `square360/pantheon-github-workflows` Composer package and will be automatically updated:

- `deploy-to-dev.yml` - Deploys merged pull requests to Pantheon DEV environment
- `deploy-multidev.yml` - Deploys pull requests to temporary multidev environments

## Configuration

### Required Repository Secrets

Add these secrets in your GitHub repository settings:

- `PANTHEON_SSH_KEY` - SSH private key for Pantheon access
- `PANTHEON_MACHINE_TOKEN` - Pantheon machine token for Terminus CLI
- `CI_GH_TOKEN` - GitHub token with repo permissions (for semantic release)

### Required Repository Variables

Add these variables in your GitHub repository settings:

- `PANTHEON_SITE` - Your Pantheon site machine name

## Custom Workflows

To add custom workflows that won't be overwritten by package updates, use different filenames such as:

- `custom-deployment.yml`
- `testing.yml`
- `build-assets.yml`

## Package Management

These workflow files are automatically managed. To update to the latest versions:

```bash
composer update square360/pantheon-github-workflows
```

For more information, see: https://github.com/Square360/pantheon-github-workflows

EOD;
  }

}