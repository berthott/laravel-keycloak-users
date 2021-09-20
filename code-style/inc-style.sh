
# RENDERERS At the moment PHPMD comes with the following five renderers:
#
# xml, which formats the report as XML.
# text, simple textual format.
# ansi, colorful, formatted text for the command line.
# html, single HTML file with possible problems.
# json, formats JSON report.
# github, a format that GitHub Actions understands (see CI Integration).
#
#
# RULESETS: cleancode, codesize, controversial, design, naming, unusedcode.
#
# Clean Code Rules: The Clean Code ruleset contains rules that enforce a clean code base. This includes rules from SOLID and object calisthenics.
# Code Size Rules: The Code Size Ruleset contains a collection of rules that find code size related problems.
# Controversial Rules: This ruleset contains a collection of controversial rules.
# Design Rules: The Design Ruleset contains a collection of rules that find software design related problems.
# Naming Rules: The Naming Ruleset contains a collection of rules about names - too long, too short, and so forth.
# Unused Code Rules: The Unused Code Ruleset contains a collection of rules that find unused code.

ERRORS="0"
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SCRIPTDIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC=$DIR/src
PHPMD="$SCRIPTDIR/check-style/vendor/phpmd/phpmd/src/bin/phpmd"
PHPMD_CONFIG="$SCRIPTDIR/check-style/phpmd_config.xml"
PHPCS_FIXER="$SCRIPTDIR/check-style/vendor/bin/php-cs-fixer"

if [ ! -d "${SCRIPTDIR}/check-style" ]; then
    mkdir "${SCRIPTDIR}/check-style"
fi

if [ ! -f "$PHPMD" ]; then
    echo "installing phpmd"
    composer require --working-dir=${SCRIPTDIR}/check-style phpmd/phpmd
fi

if [ ! -f "$PHPCS_FIXER" ]; then
    echo "installing cs-fixer"
    composer require --working-dir=${SCRIPTDIR}/check-style friendsofphp/php-cs-fixer
fi

if [ "" != "$1" ]; then
    SRC=$DIR/$1
    echo checking $SRC
fi


CSCONFIG="$SCRIPTDIR/check-style/.php-cs-fixer.dist.php"
if [ ! -f "$CONFIG" ]; then
cat << 'EOF' > $CSCONFIG
<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('bin') // exclude directories
    ->notPath('_ide_helper_models.php')
    ->notPath('_ide_helper.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true, // @PSR12
    'strict_param' => true,
    "phpdoc_annotation_without_dot" => true,
    "self_accessor" => true,
    "combine_consecutive_unsets" => true
])->setFinder($finder);

EOF
fi

