.SUFFIXES: .php .xml

.php.xml:
	php $<
	cp $@ ../$(subst .xml,,$@)

all:: blocks.xml fancy.xml gradient.xml minimal.xml mobile.xml

blocks.xml: blocks.php blocks_filter_blockquote.php blocks_filter_links.php

gradient.xml: gradient.php gradient_filter_blockquote.php

force::
	touch *.php; make all
