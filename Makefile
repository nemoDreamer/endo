default:
	# available tasks:
	# - install: registers git submodules
	# - sample: installs sample app as symlinks

install:
	git submodule init
	git submodule update

sample:
	_sample/install.sh
