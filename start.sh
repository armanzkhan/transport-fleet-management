#!/bin/bash

# Simple start script for Railway
echo "🚀 Starting Laravel application..."

# Run pre-start script
bash pre-start.sh

# Start the server
echo "🌐 Starting server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
