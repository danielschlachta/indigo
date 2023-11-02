#
# This generates the .xml files in the subdirectories - unless those 
# have been altered.
#
# Use at your own risk!
#
# Type 'make all' to update the files; 'make force' also ignores timestamps.
#

warning::
	@grep '#' Makefile | grep -v grep

all:: \
	blocks/blocks.xml

force::
	touch *.php; make all

blocks/blocks.xml: blocks.php
	php $<
