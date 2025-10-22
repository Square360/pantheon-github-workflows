# Workflow Configuration

This directory contains the core logic for the Square360 Pantheon GitHub Workflows Composer package.

## Architecture

This package follows the same pattern as Square360's Copilot Instructions package, inspired by [Pantheon's upstream-configuration approach](https://github.com/pantheon-upstreams/drupal-composer-managed).

### Design Philosophy

**Aggressive Workflow Management**: Unlike the Copilot Instructions package which protects existing files, this package **intentionally overwrites** workflow files to ensure:

- **Security Updates**: All projects get the latest security patches automatically
- **Best Practices**: Workflow improvements are deployed across all projects
- **Consistency**: All Square360 projects use identical deployment patterns
- **Zero Maintenance**: Developers don't need to manually update workflows

### Directory Structure

```
workflow-configuration/
├── scripts/
│   ├── ComposerPlugin.php      # Composer plugin registration
│   └── ComposerScripts.php     # Core installation logic
├── templates/
│   ├── deploy-to-dev.yml       # DEV deployment workflow
│   └── deploy-multidev.yml     # Multidev deployment workflow
└── README.md                   # This file
```

## How It Works

When a project requires this package via Composer:

1. **Installation** - Package installs to `vendor/square360/pantheon-github-workflows/`
2. **Plugin Registration** - Composer loads `ComposerPlugin.php` automatically
3. **Event Subscription** - Plugin subscribes to post-install and post-update events
4. **File Installation** - `ComposerScripts.php` runs and copies workflow files
5. **Overwrite Strategy** - Existing workflow files are **always overwritten**
6. **Documentation** - Creates README and changelog files (if they don't exist)

## Key Components

### ComposerPlugin.php

Implements Composer's `PluginInterface` and `EventSubscriberInterface` to automatically register event handlers.

### ComposerScripts.php

**Core Methods:**
- `postInstall()` - Runs after `composer install`
- `postUpdate()` - Runs after `composer update`  
- `installFiles()` - Main installation logic
- `getDefaultChangelogTemplate()` - Creates workflow changelog
- `getWorkflowReadmeTemplate()` - Creates workflow documentation

**File Management Strategy:**
- **Workflow Files**: Always overwritten (`deploy-to-dev.yml`, `deploy-multidev.yml`)
- **Documentation**: Created only if missing (`README.md`, `CHANGELOG-WORKFLOWS.md`)
- **Custom Files**: Preserved (any workflow file with a different name)

## Development Workflow

### Making Changes

1. **Update templates** in `templates/` directory
2. **Test locally** in a Drupal project
3. **Version and tag** the package
4. **Deploy to projects** via `composer update`

### Testing

```bash
# In a test Drupal project
composer config repositories.local path /path/to/square360-pantheon-github-workflows
composer require square360/pantheon-github-workflows:@dev

# Verify:
# - Files appear in .github/workflows/
# - Existing workflow files are overwritten
# - Custom workflow files are preserved
# - Documentation files are created
```

### Adding New Workflows

1. Add template file to `templates/` directory
2. Add filename to `$workflowFiles` array in `ComposerScripts.php`
3. Test installation process
4. Update documentation

## Differences from Copilot Instructions

| Aspect | Copilot Instructions | Pantheon Workflows |
|--------|---------------------|-------------------|
| **File Protection** | Protects existing files | Overwrites managed files |
| **Update Strategy** | Conservative | Aggressive |
| **Customization** | Encouraged in-place | Use different filenames |
| **Purpose** | Developer guidance | Infrastructure automation |

## Security Considerations

Since this package overwrites workflow files that control deployment, it's critical to:

- **Review all changes** before publishing updates
- **Test thoroughly** in development environments  
- **Version carefully** using semantic versioning
- **Communicate changes** to all project teams

## Version History

### v1.0.0 (Initial Release)
- Basic workflow file installation
- Overwrite strategy for consistency
- Template-based approach
- Integration with shared-pantheon-workflows repository