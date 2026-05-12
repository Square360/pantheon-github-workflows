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
- **Features**: Static tests, composer dependency diff, composer security audit (warn-only), PR comments with environment URL, optional OWASP ZAP baseline scan, automatic cleanup, optional Slack notifications

#### Security checks

Two security checks run alongside the deploy:

1. **Composer Security Audit** — Runs `composer audit` against `composer.lock` on every PR push. Posts a sticky PR comment listing any known security advisories or abandoned packages found in your dependency tree. **Warn-only**: findings never fail the workflow or block the PR, but the comment is updated on every push so the latest state is always visible.

2. **OWASP ZAP Baseline Scan** — Optional dynamic scan against the deployed multidev URL. Posts an HTML report (uploaded to S3), a PR comment summary, a ClickUp comment (if a task ID is detected), and a Slack notification (if `SLACK_CHANNEL` is set).

   Gating:
   - **`pr-*` multidevs** — opt-in. Add `[security]` or `[security TASK-ID]` to the PR body. Findings are reported but never fail the workflow.
   - **`rc-*` multidevs** — mandatory. The scan always runs on release-candidate environments and **fails the workflow on any High-severity finding**.

   Optional `[security YMAC-959]` syntax pins the ClickUp task ID for the comment; otherwise the ID is extracted from the branch name.

## Configuration

### Required Repository Secrets

Add these to your GitHub repository settings:

- `PANTHEON_SSH_KEY` - SSH private key for Pantheon access
- `PANTHEON_MACHINE_TOKEN` - Pantheon machine token for Terminus CLI
- `CI_GH_TOKEN` - GitHub token with repo permissions (for semantic release)

### Optional Repository Secrets (for Slack Notifications)

- `SLACK_BOT_TOKEN` - Slack bot token for posting notifications

### Optional Repository Secrets (for ClickUp Notifications)

- `CLICKUP_API_TOKEN` - ClickUp API token. Required to post deploy / VRT / security comments to the ClickUp task referenced by the PR
- `CLICKUP_TEAM_ID` - ClickUp team (workspace) ID

### Optional Repository Secrets (for Visual Regression Test and Security Scan Reports)

These secrets are used by both the VRT and Security Scan reusable workflows. Reports are uploaded to S3 under `${PANTHEON_SITE}/${RUN_KEY}/` (VRT artefacts) and `${PANTHEON_SITE}/${RUN_KEY}/security/` (ZAP reports), so a single bucket and IAM policy covers both.

- `AWS_ACCESS_KEY_ID` - IAM user with `s3:PutObject` on the report bucket
- `AWS_SECRET_ACCESS_KEY` - paired secret for the IAM user
- `AWS_S3_BUCKET` - bucket name (no `s3://` prefix)
- `AWS_S3_REGION` - bucket region (defaults to `us-east-1` if unset)

### Required Repository Variables

- `PANTHEON_SITE` - Your Pantheon site machine name

### Optional Repository Variables (for Slack Notifications)

- `SLACK_CHANNEL` - Slack channel for deployment notifications (e.g., `#deployments`)

### Optional Repository Variables (for Workflow Control)

- `WORKFLOW_SKIP_TERMINUS` - Set to `true` (without quotes) to skip post-deployment terminus commands (updb, cim, cr)

## Updating

To get the latest workflow versions:

```bash
composer update square360/pantheon-github-workflows
```

**Important**: The workflow files `deploy-to-dev.yml` and `deploy-multidev.yml` will be **overwritten** with the latest versions. This ensures all projects stay up-to-date with the latest standards and security updates.

### Rollout heads-up for the security audit additions

After updating to a release that includes the Composer Security Audit and OWASP ZAP Baseline Scan jobs, each consumer site will see new workflow activity on its next PR:

- **Composer Security Audit** — runs on every PR push. Sites whose `composer.lock` currently contains a package with a known advisory will see a sticky `composer-security-audit` PR comment listing the findings. **The workflow status stays green** — this is a warn-only check.
- **OWASP ZAP Baseline Scan** — opt-in on `pr-*` PRs (no behaviour change unless `[security]` is added to the PR body), mandatory on `rc-*` merges to `develop`. The first `rc-*` build after the update will run a ZAP scan against the release-candidate multidev and will fail if any High-severity finding is reported. Plan the first post-update RC merge accordingly.

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