#!/bin/bash

SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

. ${SCRIPTDIR}/inc-style.sh

mdFailures=$(php "$PHPMD" $SRC text $PHPMD_CONFIG)

if [ ! "0" = "$?" ]; then
    MD_COUNT=$(wc -l <<<$mdFailures)
    echo -e "\x1B[31m✖ PHP md code style failure count:\x1B[0m\n\n$MD_COUNT"
    ERRORS="1"
fi


csFailures=$(php "$PHPCS_FIXER" fix $SRC \
    --dry-run --diff --allow-risky=yes --config $CSCONFIG)

if [ ! "0" = "$?" ]; then
    echo -e "\x1B[31m✖ PHP cs code style failures:\x1B[0m\n\n$csFailures"
    CS_FILES=$(grep -E '^...?[0-9]{0,2}\)' <<<$csFailures)
    CS_ERR_COUNT=$(wc -l <<<$CS_FILES)
    ERRORS="1"
fi

if [ "" != "$MD_COUNT" ]; then
    echo -e "\x1B[31m\n✖ PHP Mess Detector Warnings:\x1B[0m\n\n$mdFailures"
    echo -e "\x1b[33m✖ PHP Mess Detector Warnings: $MD_COUNT\x1b[0m"

fi

if [ "" != "$CS_FILES" ]; then
    echo -e "\x1B[31m✖ PHP Coding Standard Errors in $CS_ERR_COUNT files\x1B[0m"
fi

if [ "0" != "$ERRORS" ]
then
    echo -e "\x1B[31m✖ There were errors in check-style. Exiting with Error-Code 1.\x1B[0m\n"
    exit 1
fi
