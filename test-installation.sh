#!/bin/bash

# Square360 Pantheon GitHub Workflows Package - Installation Test Script
# This script demonstrates how to install and test the package

echo "🚀 Testing Square360 Pantheon GitHub Workflows Package"
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Please run this script from a Drupal project root directory"
    exit 1
fi

echo "✅ Drupal project detected"

# Add the package repository (local development)
echo "📝 Adding local package repository..."
composer config repositories.pantheon-workflows path /Volumes/Work/repos/pantheon-github-workflows

# Install the package
echo "📦 Installing package..."
composer require square360/pantheon-github-workflows:@dev

# Check results
echo "🔍 Checking installation results..."

if [ -f ".github/workflows/deploy-to-dev.yml" ]; then
    echo "✅ deploy-to-dev.yml installed"
else
    echo "❌ deploy-to-dev.yml missing"
fi

if [ -f ".github/workflows/deploy-multidev.yml" ]; then
    echo "✅ deploy-multidev.yml installed"
else
    echo "❌ deploy-multidev.yml missing"
fi

if [ -f "CHANGELOG-WORKFLOWS.md" ]; then
    echo "✅ CHANGELOG-WORKFLOWS.md created"
else
    echo "❌ CHANGELOG-WORKFLOWS.md missing"
fi

if [ -f ".github/workflows/README.md" ]; then
    echo "✅ .github/workflows/README.md created"
else
    echo "❌ .github/workflows/README.md missing"
fi

echo ""
echo "🎉 Installation test complete!"
echo ""
echo "📋 Next steps:"
echo "1. Configure GitHub repository secrets:"
echo "   - PANTHEON_SSH_KEY"
echo "   - PANTHEON_MACHINE_TOKEN"
echo "   - CI_GH_TOKEN"
echo ""
echo "2. Configure GitHub repository variables:"
echo "   - PANTHEON_SITE"
echo ""
echo "3. Test workflow updates:"
echo "   composer update square360/pantheon-github-workflows"
echo ""
echo "📚 See .github/workflows/README.md for detailed configuration"