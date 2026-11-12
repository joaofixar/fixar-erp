#!/bin/bash
echo "<h2>HARDNESS3 ($1)</h2>"
cd /var/www/$1/hardness3/
ionice -c 3 svn up $2
echo "<h2>HARDNESS ($1)</h2>"
cd /var/www/$1/hardness/
ionice -c 3 svn up $2
