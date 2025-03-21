#!/bin/bash

if [ ! -f "composer.json" ]; then
  echo "❌ Error: Please run this script from the project root directory."
  exit 1
fi

echo "Setting up pre-commit hook..."
cat << 'EOF' > .git/hooks/pre-commit
#!/bin/sh
echo "Running Composer Pint..."
composer pint
if [ $? -ne 0 ]; then
    echo "❌ Composer Pint found issues. Commit aborted."
    exit 1
fi

if ! git diff --exit-code > /dev/null; then
    echo "❌ Composer Pint fixed formatting issues. Please review and re-add the changes."
    echo "    Use 'git diff' to see the changes, then 'git add .' to re-stage them."
    exit 1
fi

echo "Running Composer PHPStan..."
composer phpstan
if [ $? -ne 0 ]; then
    echo "❌ PHPStan found issues. Commit aborted."
    exit 1
fi

echo "Running PHP Artisan Test..."
php artisan test
if [ $? -ne 0 ]; then
    echo "❌ PHP Artisan Test found issues. Commit aborted."
    exit 1
fi

echo "✅ All checks passed. Proceeding with commit."
EOF

chmod +x .git/hooks/pre-commit

echo "✅ Pre-commit hook setup complete!"
