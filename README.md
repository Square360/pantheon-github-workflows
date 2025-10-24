# Square360 Pantheon GitHub Workflows

A Composer package that automatically installs and manages standardized GitHub Actions workflows for Pantheon deployments in Drupal projects.

## Features

- **Automatic Installation**: Workflows are automatically installed to `.github/workflows/` when you require the package
- **Always Up-to-Date**: Workflow files are updated to the latest version on `composer update`
- **Overwrite Protection**: Custom workflows with different names are preserved
- **Zero Configuration**: Works out of the box with Square360's shared workflow repository
- **Pantheon Optimized**: Specifically designed for Pantheon hosting platform

## Installation

```bash
composer require square360/pantheon-github-workflows
```

**What happens during installation:**
- Creates `.github/workflows/` directory
- Installs `deploy-to-dev.yml` for DEV deployments  
- Installs `deploy-multidev.yml` for PR-based multidev deployments
- Creates `CHANGELOG-WORKFLOWS.md` for tracking workflow changes
- Creates `.github/workflows/README.md` with configuration instructions

## Workflow Files

### deploy-to-dev.yml
- **Trigger**: When pull requests are merged to master/main
- **Action**: Deploys code to Pantheon DEV environment
- **Features**: Includes semantic release, backup checks, database updates, optional Slack notifications

### deploy-multidev.yml  
- **Trigger**: Pull request opened/updated on feature branches
- **Action**: Creates temporary multidev environment for testing
- **Features**: Static tests, PR comments with environment URL, automatic cleanup, optional Slack notifications

## Configuration

### Required Repository Secrets

Add these to your GitHub repository settings:

- `PANTHEON_SSH_KEY` - SSH private key for Pantheon access
- `PANTHEON_MACHINE_TOKEN` - Pantheon machine token for Terminus CLI  
- `CI_GH_TOKEN` - GitHub token with repo permissions (for semantic release)

### Optional Repository Secrets (for Slack Notifications)

- `SLACK_BOT_TOKEN` - Slack bot token for posting notifications

### Required Repository Variables

- `PANTHEON_SITE` - Your Pantheon site machine name

### Optional Repository Variables (for Slack Notifications)

- `SLACK_CHANNEL` - Slack channel for deployment notifications (e.g., `#deployments`)

## Updating

To get the latest workflow versions:

```bash
composer update square360/pantheon-github-workflows
```

**Important**: The workflow files `deploy-to-dev.yml` and `deploy-multidev.yml` will be **overwritten** with the latest versions. This ensures all projects stay up-to-date with the latest standards and security updates.

## Custom Workflows

To add custom workflows that won't be overwritten, use different filenames:

```
.github/workflows/
├── deploy-to-dev.yml         ← Managed by package (overwritten)
├── deploy-multidev.yml       ← Managed by package (overwritten)  
├── custom-deployment.yml     ← Your custom workflow (preserved)
├── testing.yml              ← Your custom workflow (preserved)
└── build-assets.yml         ← Your custom workflow (preserved)
```

## Dependencies

This package depends on the shared workflow repository:
- [Square360/shared-pantheon-workflows](https://github.com/Square360/shared-pantheon-workflows)

The workflows use reusable workflows from this central repository to ensure consistency across all Square360 projects.

## Architecture

This package uses a Composer plugin approach inspired by [Pantheon's upstream-configuration pattern](https://github.com/pantheon-upstreams/drupal-composer-managed). The plugin automatically runs after `composer install` and `composer update` to ensure workflow files are always current.

**Design Philosophy:**
- **Aggressive Updates**: Workflow files are always overwritten to ensure security and best practices
- **Selective Preservation**: Only custom files with different names are preserved
- **Zero Maintenance**: Projects get updates automatically without manual intervention

## Development

See `workflow-configuration/README.md` for development documentation.

## Support

For issues or questions:
- Create an issue in this repository
- Contact the DevOps team at Square360

## License

MIT License - see LICENSE file for details.