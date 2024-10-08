#!/usr/bin/env bash

vendor/bin/phpcbf $@

status=$?

[ $status -eq 1 ] && exit 0 || exit $status
