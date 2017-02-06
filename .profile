# Run the cache scripts
composer warmup

# Run the job queue
nohup php artisan queue:work --daemon --sleep=30 --tries=3 > /dev/null 2>&1 &