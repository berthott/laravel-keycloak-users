#!/bin/bash

SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. ${SCRIPTDIR}/inc-style.sh
php $PHPCS_FIXER fix $SRC --allow-risky=yes --config $CSCONFIG
