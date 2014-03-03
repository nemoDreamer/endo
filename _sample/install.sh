#!/usr/bin/env bash
#
# Creates symlinks of sample app files/folders in root.

SOURCE_DIR="$(dirname $0)/"
TARGET_DIR="$SOURCE_DIR../../"

for NODE in app .htaccess
do
	SOURCE=${SOURCE_DIR}${NODE}
	TARGET=${TARGET_DIR}${NODE}
	# [ ! -e $TARGET ] && [ ! -L $TARGET ] && ln -s $SOURCE $TARGET
	ln -s $SOURCE $TARGET
done
