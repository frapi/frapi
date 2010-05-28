#!/bin/sh

##
# This is obviously not a very advanced installer
# in fact it doesn't install much. 
#
# If you would be interested in making an actual installer
# please send an email to our developers mailing list at
# http://groups.google.com/group/frapi-dev
##

CURRENTDIR=`pwd`

# Giving permissions
## This is for the generated actions (By the administration interface)
chmod 775 $CURRENTDIR/src/frapi/custom/Action

## This is for the likes of errors and actions 
## saved by the administrator interface
chmod 775 $CURRENTDIR/src/frapi/admin/application/config/app
chmod 664 $CURRENTDIR/src/frapi/admin/application/config/app/*.xml

# Init the Armchair submodule
git submodule init
git submodule update
