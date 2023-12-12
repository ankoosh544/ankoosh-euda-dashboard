#!/bin/bash

while true
do
    /usr/bin/php /var/www/html/github/ankoosh-euda-dashboard/euda-dashboard-main/artisan app:s3-monitor
    sleep 10
done
	
