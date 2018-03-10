SHELL=/bin/sh

prefix=/usr
bindir=/usr/bin
sysconfdir=/etc
phpdir=user/share/php

src=$(CURDIR)/src/Frd

#command

COPY=cp
RM=rm

default: test
test:
		@echo $(CURDIR)
install:
		[  -d $(phpdir)/Frd ] && echo "uninstall first" && exit 1

		$(COPY) -r $(src)  $(phpdir)/


uninstall:
		$(RM) -rf   $(phpdir)/Frd

