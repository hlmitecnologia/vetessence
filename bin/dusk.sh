#!/bin/bash
# Helper script to run Dusk tests with proper environment setup
set -e

APP_URL="${APP_URL:-http://127.0.0.1:8000}"
DB_DATABASE="${DB_DATABASE:-vetessence_testing}"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

cleanup() {
    echo "Cleaning up..."
    kill $SERVER_PID 2>/dev/null || true
    exit 0
}

trap cleanup EXIT INT TERM

# Kill any existing processes
kill $(lsof -ti:9515) 2>/dev/null || true
kill $(lsof -ti:8000) 2>/dev/null || true
sleep 1

echo "Starting ChromeDriver..."
vendor/laravel/dusk/bin/chromedriver-linux --port=9515 > /dev/null 2>&1 &
CHROME_PID=$!
sleep 2

echo "Starting PHP server..."
php artisan serve --env=dusk.local --port=8000 --host=127.0.0.1 > /dev/null 2>&1 &
SERVER_PID=$!
sleep 2

# Verify both services
if ! curl -sf http://127.0.0.1:9515/status > /dev/null 2>&1; then
    echo -e "${RED}ChromeDriver failed to start${NC}"
    exit 1
fi

if ! curl -sf -o /dev/null http://127.0.0.1:8000/; then
    echo -e "${RED}PHP server failed to start${NC}"
    exit 1
fi

echo -e "${GREEN}Services ready. Running tests...${NC}"

APP_URL=http://127.0.0.1:8000 \
DUSK_DRIVER_URL=http://127.0.0.1:9515 \
php vendor/bin/phpunit -c phpunit.dusk.xml "$@"
EXIT_CODE=$?

echo -e "${GREEN}Tests completed with exit code: $EXIT_CODE${NC}"
exit $EXIT_CODE
