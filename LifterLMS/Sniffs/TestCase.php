<?php

/**
 * Abstract TestCase file.
 *
 * @package LifterLMSCS\LifterLMS\Sniffs
 *
 * @since [version]
 * @version [version]
 */

declare( strict_types=1 );

namespace LifterLMSCS\LifterLMS\Sniffs;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;

/**
 * Base PHPUNit TestCase class.
 *
 * @since [version]
 */
abstract class TestCase extends \SlevomatCodingStandard\Sniffs\TestCase {

	/**
	 * Retrieves the tested sniffs name without the LifterLMSCS prefix.
	 *
	 * @since [version]
	 *
	 * @return string The sniff name.
	 */
	private static function get_sniff_name_without_prefix(): string {
		$reflector = new \ReflectionClass( static::class );
		$method    = $reflector->getMethod( 'getSniffName' );
		$method->setAccessible( true );
		$name = $method->invoke( null );
		return str_replace( 'LifterLMSCS.', '', $name );
	}

	/**
	 * Runs PHPCS against the provided file.
	 *
	 * @param string   $file_path      Absolute path to the tested file.
	 * @param array    $sniff_props    Sniff property customizations.
	 * @param string[] $codes_to_check Only check the specified error codes.
	 * @param string[] $cli_args       CLI Args to pass to PHPCS.
	 */
	protected static function checkFile(
		string $file_path,
		array $sniff_props = [],
		array $codes_to_check = [],
		array $cli_args = []
	): File {

		if ( defined( 'PHP_CODESNIFFER_CBF' ) === false ) {
			define( 'PHP_CODESNIFFER_CBF', false );
		}
		$code_sniffer         = new Runner();
		$code_sniffer->config = new Config( array_merge( [ '-s' ], $cli_args ) );
		$code_sniffer->init();

		if ( count( $sniff_props ) > 0 ) {
			$code_sniffer->ruleset->ruleset[ self::get_sniff_name_without_prefix() ]['properties'] = $sniff_props;
		}

		$reflector           = new \ReflectionClass( static::class );
		$get_sniff_classname = $reflector->getMethod( 'getSniffClassName' );
		$get_sniff_classname->setAccessible( true );

		$sniff_classname = $get_sniff_classname->invoke( null );

		$sniff = new $sniff_classname();

		$code_sniffer->ruleset->sniffs = [ $sniff_classname => $sniff ];

		if ( count( $codes_to_check ) > 0 ) {
			foreach ( self::getSniffClassReflection()->getConstants() as $const_name => $const_val ) {
				if ( strpos( $const_name, 'CODE_' ) !== 0 || in_array( $const_val, $codes_to_check, true ) ) {
					continue;
				}

				$index = sprintf( '%s.%s', self::get_sniff_name_without_prefix(), $const_val );
				$code_sniffer->ruleset->ruleset[ $index ]['severity'] = 0;
			}
		}

		$code_sniffer->ruleset->populateTokenListeners();

		$file = new LocalFile( $file_path, $code_sniffer->ruleset, $code_sniffer->config );
		$file->process();

		return $file;
	}

	/**
	 * Runs PHPCS against the file for the specified test method.
	 *
	 * @param string   $method            The full qualified test method name.
	 * @param string   $test_dir_path     The full path to the test directory.
	 * @param array    $sniff_props Sniff property customizations.
	 * @param string[] $codes_to_check    Only check the specified error codes.
	 * @param string[] $cli_args          CLI Args to pass to PHPCS.
	 */
	protected static function checkFileForTest(
		string $method,
		string $test_dir_path,
		array $sniff_props = [],
		array $codes_to_check = [],
		array $cli_args = []
	): File {

		$sniff = explode( '\\', $method );
		$sniff = lcfirst( end( $sniff ) );
		$sniff = explode( 'Test::test_', $sniff );

		$test = explode( '_', $sniff[1] );
		$test = array_map( 'ucfirst', $test );
		$test = implode( '', $test );

		$file_path = $test_dir_path . '/data/' . $sniff[0] . $test . '.php';
		if ( ! file_exists( $file_path ) ) {
			touch( $file_path );
		}

		return self::checkFile( $file_path, $sniff_props, $codes_to_check, $cli_args );
	}

	/**
	 * Asserts a sniff error.
	 *
	 * @since [version]
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file File being scanned.
	 * @param int                         $line       Line number where errors are expected.
	 * @param string                      $code       Expected sniff error code.
	 * @param string|null                 $message    Expected sniff error message (optional).
	 */
	protected static function assertSniffError(
		File $phpcs_file,
		int $line,
		string $code,
		?string $message = null
	): void {

		$reflector          = new \ReflectionClass( static::class );
		$has_error          = $reflector->getMethod( 'hasError' );
		$get_formatted_errs = $reflector->getMethod( 'getFormattedErrors' );

		$has_error->setAccessible( true );
		$get_formatted_errs->setAccessible( true );

		$errors = $phpcs_file->getErrors();
		self::assertTrue( isset( $errors[ $line ] ), sprintf( 'Expected error on line %s, but none found.', $line ) );

		$sniff_code = sprintf( '%s.%s', self::get_sniff_name_without_prefix(), $code );

		self::assertTrue(
			$has_error->invoke( null, $errors[ $line ], $sniff_code, $message ),
			sprintf(
				'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
				$sniff_code,
				null !== $message
					? sprintf( ' with message "%s"', $message )
					: '',
				$line,
				PHP_EOL . PHP_EOL,
				$line,
				PHP_EOL,
				$get_formatted_errs->invoke( null, $errors[ $line ] ),
				PHP_EOL
			)
		);

	}

}
