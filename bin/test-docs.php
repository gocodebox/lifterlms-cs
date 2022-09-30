<?php

/**
 * Ruleset documentation test
 *
 * Tests that all sniffs used by the specified ruleset are documented / referenced in the ruleset file.
 *
 * @package LifterLMSCS\Bin
 *
 * @since [version]
 * @version [version]
 */

declare( strict_types=1 );

namespace LifterLMSCS\Bin;

$standard = $argv[1] ?? null;

if ( ! $standard ) {
	echo PHP_EOL;
	echo "Usage: {$argv[0]} <standard>" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo PHP_EOL;
	echo '<standard> Standard to check' . PHP_EOL;
	echo PHP_EOL;
	die( 0 );
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use XMLReader;

const ROOT_DIR = __DIR__ . '/..';

$reader = new XMLReader();
$reader->open( ROOT_DIR . "/{$standard}/ruleset.xml" );

$documented_sniffs = [];

while ( $reader->read() ) {

	$sniff = null;
	if ( XMLReader::COMMENT === $reader->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$line = str_replace(
			[ '<!--', '-->' ],
			[ '', '' ],
			$reader->readOuterXml()
		);
		$line = trim( $line );
		if ( false === strpos( $line, '@see' ) ) {
			continue;
		}
		$sniff = trim( str_replace( '@see', '', $line ) );
	} elseif ( XMLReader::ELEMENT === $reader->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$node = $reader->expand();
		if ( 'rule' === $node->nodeName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$sniff = $node->attributes->getNamedItem( 'ref' )->value;
			// Only use the first 3 parts of the sniff (namespace.type.rule) and ignore the rule settings/properties.
			$sniff = implode( '.', array_slice( explode( '.', $sniff ), 0, 3 ) );
		}
	}

	if ( $sniff ) {
		$documented_sniffs[] = $sniff;
	}
}


// Parse the explanation into an array.
if ( ! defined( 'PHP_CODESNIFFER_CBF' ) ) {
	define( 'PHP_CODESNIFFER_CBF', false );
}

if ( ! defined( 'PHP_CODESNIFFER_VERBOSITY' ) ) {
	define( 'PHP_CODESNIFFER_VERBOSITY', 0 );
}

$runner         = new Runner();
$runner->config = new Config( [ "--standard={$standard}" ] );
$runner->init();

$config            = new Config();
$config->standards = [ $standard ];

$ruleset = new Ruleset( $config );

ob_start();
$ruleset->explain();
$explanation = ob_get_clean();

$loaded_sniffs = array_map(
	'trim',
	array_filter(
		explode( "\n", $explanation ),
		function( $line ) {
			if ( empty( $line ) ) {
				return false;
			}
			return 0 === strpos( $line, '  ', );
		}
	)
);


sort( $documented_sniffs );
sort( $loaded_sniffs );

$missing     = array_diff( $loaded_sniffs, $documented_sniffs );
$num_missing = count( $missing );

if ( 0 === $num_missing ) {
	echo PHP_EOL;
	echo 'No missing sniffs!' . PHP_EOL;
	echo PHP_EOL;
	exit( 0 );
}

echo PHP_EOL;
echo "The following {$num_missing} sniffs are not referenced in the {$standard} ruleset:" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo PHP_EOL;

foreach ( $missing as $sniff ) {
	echo "  {$sniff}" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
echo PHP_EOL;
exit( 1 );

