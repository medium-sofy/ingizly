#!/bin/sh

# Wait for the database to be ready
while ! nc -z db 3306; do
  echo "Waiting for the database..."
  sleep 2
done

# Run migrations and seed the database
php artisan migrate --force --seed

# Start the Laravel server and frontend development server
php artisan serve --host=0.0.0.0 --port=8000 &
npm run dev