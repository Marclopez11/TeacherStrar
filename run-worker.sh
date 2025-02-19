#!/bin/bash
php artisan queue:work --tries=3 --timeout=90
