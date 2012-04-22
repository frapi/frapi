#!/bin/bash

wget http://pecl.php.net/get/APC
tar -xzf APC
sh -c "cd APC-* && phpize && ./configure && sudo make install"
echo "extension=apc.so
[apc]
apc.enabled = 1
apc.enable_cli = 1" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
