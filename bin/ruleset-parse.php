<?php
namespace LifterLMSCS\Bin;

$ruleset = $argv[1] ?? null;

if ( ! $ruleset ) {
	echo "Usage {$argv[0]} <rulesetPath>" . PHP_EOL;
	die( 0 );
}

use XMLReader;

const ROOT_DIR = __DIR__ . '/..';
const START_TOKEN = '<!-- Parser-Start-Token -->';
const NEWLINE = "\r";

$start_token_found = false;
$is_title_line     = false;
$is_in_list        = false;

$standard = [];

function format_line( $line, $add_new_line = true ) {

	// Strip comment tags.
	$line = str_replace(
		[ '<!--', '-->' ],
		'',
		$line
	);

	// Trim whitespace.
	$line = trim( $line );

	// Add Markdown headers based on the list notation.
	// 1. = #; 1.2 = ##; 1.2.3 = ###; etc...
	if ( preg_match( '/^(\d{1,3}\.)(\d{1,3})?/', $line, $header_matches ) ) {
		$level  = count( array_filter( explode( '.', $header_matches[0] ) ) );
		$before = '';
		if ( 1 === $level ) {
			$before = NEWLINE;
		} elseif ( 2 >= $level ) {
			// $before = NEWLINE;
		}
		$line = $before . str_repeat( '#', $level ) . " {$line}" . NEWLINE;
	} elseif ( $add_new_line ) {
		$line = $line . NEWLINE;
	}

	$line = preg_replace( '/^\t+/m', '', $line );

	return $line;

}

$reader = new XMLReader();
$reader->open( ROOT_DIR . '/' . $ruleset );

while( $reader->read() ) {
	if ( XMLReader::COMMENT === $reader->nodeType ) {

		$line = $reader->readOuterXml();

		if ( START_TOKEN === $line ) {
			$start_token_found = true;
			$is_title_line     = true;
			continue;
		} elseif ( ! $start_token_found ) {
			continue;
		} elseif ( false !== strpos( $line, '@parserIgnore' ) || false !== strpos( $line, '@see' ) ) {
			continue;
		}


		$line = format_line( $line, ! $is_title_line );

		$first_char = substr( $line, 0, 1 );

		// First line in a list.
		if ( '+' === $first_char && ! $is_in_list ) {
			$is_in_list = true;
		}

		if ( $is_in_list && '+' === $first_char ) {
			$line = trim( $line );
		}

		// Finished with list.
		if ( $is_in_list && '+' !== $first_char ) {
			$is_in_list = false;
			$line = NEWLINE . $line;
		}

		$standard[] = $line;



		if ( $is_title_line ) {
			$is_title_line = false;
			$standard[] = str_repeat( '=', strlen( $line ) );
			$standard[] = '';
			$time       = gmdate( 'Y-m-d\TH:i:s\Z', time() );
			$standard[] = "<!-- These docs were automatically generated from the {$ruleset} file on {$time}. -->";
			$standard[] = '';
		}


	}
}

$fh = fopen( ROOT_DIR . '/docs/php-modern-coding-standard.md', 'w' );

fwrite( $fh, implode( "\r", $standard ) );
