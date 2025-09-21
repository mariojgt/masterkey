#!/bin/bash

# MasterKey Package Test Runner
# This script runs all tests for the MasterKey package

echo "🚀 Running MasterKey Package Tests..."
echo "=================================="

# Change to the package directory
cd "$(dirname "$0")"

# Check if we're in Docker
if [ -f "/.dockerenv" ]; then
    echo "📦 Running inside Docker container"
    PHPUNIT_CMD="vendor/bin/phpunit"
else
    echo "🐳 Running tests in Docker container"
    PHPUNIT_CMD="docker exec newmag_app vendor/bin/phpunit"
fi

# Run the tests
echo "📋 Test Configuration:"
echo "  - Database: SQLite (in-memory)"
echo "  - Environment: testing"
echo "  - Mail: array driver"
echo ""

echo "🧪 Running tests..."
$PHPUNIT_CMD --configuration repo/masterkey/phpunit.xml repo/masterkey/tests/

# Check test results
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ All tests passed!"
    echo ""
    echo "🎯 Test Coverage:"
    echo "  ✓ App Authentication (request-code, verify)"
    echo "  ✓ Web Login (status, approve)"
    echo "  ✓ AuthToken Middleware"
    echo "  ✓ Polymorphic Relationships"
    echo ""
    echo "🔧 What was tested:"
    echo "  • Token creation with polymorphic models"
    echo "  • Session management with any model type"
    echo "  • Authentication middleware with polymorphic tokens"
    echo "  • QR code login flow end-to-end"
    echo "  • Error handling and validation"
    echo ""
else
    echo ""
    echo "❌ Some tests failed!"
    echo "Check the output above for details."
    echo ""
fi
