#!/bin/bash
wget "http://pecl.php.net/get/APC"
tar -xzf APC
cd APC-*
phpize
./configure
sudo make install
INI_DIR=`php --ini | grep "Scan for additional" | sed -e "s|.*:\s*||"`
INI_FILE="$INI_DIR/apc.ini"
echo "extension=apc.so
[apc]
apc.enabled = 1
apc.enable_cli = 1" > $INI_FILE
