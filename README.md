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
- Installs `deploy-multidev.yml` for PR-based and release-candidate multidev deployments
- Installs `deploy-epic-multidev.yml` for epic-branch multidev deployments
- Creates `CHANGELOG-WORKFLOWS.md` for tracking workflow changes
- Creates `.github/workflows/README.md` with configuration instructions

## Workflow Files

### deploy-to-dev.yml
- **Trigger**: When pull requests are merged to master/main
- **Action**: Deploys code to Pantheon DEV environment
- **Features**: Includes semantic release, backup checks, database updates, optional Slack notifications

### deploy-multidev.yml
- **Trigger**: Pull request opened/updated/closed on feature branches (any branch except master/main)
- **Action**:
  - PRs opened/synced/reopened on any branch → deploys to `pr-NNN` multidev
  - PR closed-and-merged into `develop` → deploys to `rc-YYYY-WW` release-candidate multidev
- **Features**: Static tests, composer dependency diff, composer security audit (warn-only); the underlying reusable workflow handles deploy, opt-in OWASP ZAP baseline scan, opt-in Visual Regression Tests, ClickUp comments, and optional Slack notifications

### deploy-epic-multidev.yml
- **Trigger**: Push to an `epic/**` branch (e.g. `epic/CU-EPIC-123` or `epic/CU-EPIC-123-checkout-redesign`)
- **Action**: Deploys to an `epr-NNN` multidev whose name is derived from the epic ticket ID in the branch name
- **Features**: Mandatory strict-mode OWASP ZAP baseline scan (fails the workflow on any High-severity finding), optional Slack and ClickUp notifications

#### Security checks

Two security checks run alongside the deploy:

1. **Composer Security Audit** — Runs `composer audit` against `composer.lock` on every PR push. Posts a sticky PR comment listing any known security advisories or abandoned packages found in your dependency tree. **Warn-only**: findings never fail the workflow or block the PR, but the comment is updated on every push so the latest state is always visible.

2. **OWASP ZAP Baseline Scan** — Dynamic scan against the deployed multidev URL. Posts an HTML report (uploaded to S3), a PR comment summary, a ClickUp comment (if a task ID is detected), and a Slack notification (if `SLACK_CHANNEL` is set).

   Gating:
   - **`pr-*` multidevs** — opt-in. Add `[run-security]` or `[run-security TASK-ID]` to the PR body. Findings are reported but never fail the workflow. (The legacy `[security]` / `[security TASK-ID]` flags continue to work.)
   - **`rc-*` multidevs** — mandatory. The scan always runs on release-candidate environments and **fails the workflow on any High-severity finding**.
   - **`epr-*` multidevs** — mandatory. Same strict gate as `rc-*`; runs on every push to the epic branch.

   Optional `[run-security YMAC-959]` syntax pins the ClickUp task ID for the comment; otherwise the ID is extracted from the branch name.

#### Visual Regression Tests

Opt-in on `pr-*` multidevs: add `[run-vrt]` or `[run-vrt TASK-ID]` to the PR body. (The legacy `[vrt]` / `[vrt TASK-ID]` flags continue to work.) The reusable workflow uploads HTML and JSON reports to S3 and posts the same PR / ClickUp / Slack notification pattern as the security scan.

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

### Rollout heads-up for the v3 split

The shared workflows that back these templates have been split out by environment type and now live in the renamed `Square360/shared-workflows` repository (pinned to `v3.0.0`). When you update to this release, each consumer site picks up:

- A new `deploy-epic-multidev.yml` workflow that fires on pushes to `epic/**` branches and creates an `epr-NNN` multidev with a mandatory strict-mode ZAP scan. If you don't use epic branches, the file is harmless — it never triggers.
- The existing `deploy-multidev.yml` workflow now routes PR pushes to one shared workflow and `develop`-merge events to another. Behaviour is unchanged for consumers; the split happens inside the reusable workflows.
- New `[run-security]` and `[run-vrt]` PR-body flags become the canonical opt-in tags. The legacy `[security]` and `[vrt]` flags continue to work indefinitely — no forced migration on PR authors.

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