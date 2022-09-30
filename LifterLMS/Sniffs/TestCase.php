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
 * @codeCoverageIgnore
 */
abstract class TestCase extends \SlevomatCodingStandard\Sniffs\TestCase {

	private static function getSniffNameWithoutPrefix(): string {
		$reflector = new \ReflectionClass( static::class );
		$method    = $reflector->getMethod( 'getSniffName' );
		$method->setAccessible( true );
		$name = $method->invoke( null );
		return str_replace( 'LifterLMSCS.', '', $name );
	}

	/**
	 * Runs PHPCS against the provided file.
	 *
	 * @param array    $sniffProperties Sniff property customizations.
	 * @param string[] $codesToCheck    Only check the specified error codes.
	 * @param string[] $cliArgs         CLI Args to pass to PHPCS.
	 */
	protected static function checkFile( string $filePath, array $sniffProperties = [], array $codesToCheck = [], array $cliArgs = [] ): File {

		if ( defined( 'PHP_CODESNIFFER_CBF' ) === false ) {
			define( 'PHP_CODESNIFFER_CBF', false );
		}
		$codeSniffer         = new Runner();
		$codeSniffer->config = new Config( array_merge( [ '-s' ], $cliArgs ) );
		$codeSniffer->init();

		if ( count( $sniffProperties ) > 0 ) {
			$codeSniffer->ruleset->ruleset[ self::getSniffNameWithoutPrefix() ]['properties'] = $sniffProperties;
		}

		$reflector         = new \ReflectionClass( static::class );
		$getSniffClassName = $reflector->getMethod( 'getSniffClassName' );

		$getSniffClassName->setAccessible( true );

		$sniffClassName = $getSniffClassName->invoke( null );
		/** @var Sniff $sniff */
		$sniff = new $sniffClassName();

		$codeSniffer->ruleset->sniffs = [ $sniffClassName => $sniff ];

		if ( count( $codesToCheck ) > 0 ) {
			foreach ( self::getSniffClassReflection()->getConstants() as $constantName => $constantValue ) {
				if ( strpos( $constantName, 'CODE_' ) !== 0 || in_array( $constantValue, $codesToCheck, true ) ) {
					continue;
				}

				$codeSniffer->ruleset->ruleset[ sprintf( '%s.%s', self::getSniffNameWithoutPrefix(), $constantValue ) ]['severity'] = 0;
			}
		}

		$codeSniffer->ruleset->populateTokenListeners();

		$file = new LocalFile( $filePath, $codeSniffer->ruleset, $codeSniffer->config );
		$file->process();

		return $file;
	}

	/**
	 * Runs PHPCS against the file for the specified test method.
	 *
	 * @param string   $method          The full qualified test method name.
	 * @param string   $testDirPath     The full path to the test directory.
	 * @param array    $sniffProperties Sniff property customizations.
	 * @param string[] $codesToCheck    Only check the specified error codes.
	 * @param string[] $cliArgs         CLI Args to pass to PHPCS.
	 */
	protected static function checkFileForTest( string $method, string $testDirPath, array $sniffProperties = [], array $codesToCheck = [], array $cliArgs = [] ): File {

		$sniff = explode( '\\', $method );
		$sniff = lcfirst( end( $sniff ) );
		$sniff = explode( 'Test::test_', $sniff );

		$test = explode( '_', $sniff[1] );
		$test = array_map( 'ucfirst', $test );
		$test = implode( '', $test );

		$filePath = $testDirPath . '/data/' . $sniff[0] . $test . '.php';
		if ( ! file_exists( $filePath ) ) {
			touch( $filePath );
			var_dump( "File created for test \"$method\" at $filePath." );
		}

		return self::checkFile( $filePath, $sniffProperties, $codesToCheck, $cliArgs );
	}

	protected static function assertSniffError( File $phpcsFile, int $line, string $code, ?string $message = null ): void {

		$reflector          = new \ReflectionClass( static::class );
		$hasError           = $reflector->getMethod( 'hasError' );
		$getFormattedErrors = $reflector->getMethod( 'getFormattedErrors' );

		$hasError->setAccessible( true );
		$getFormattedErrors->setAccessible( true );

		$errors = $phpcsFile->getErrors();
		self::assertTrue( isset( $errors[ $line ] ), sprintf( 'Expected error on line %s, but none found.', $line ) );

		$sniffCode = sprintf( '%s.%s', self::getSniffNameWithoutPrefix(), $code );

		self::assertTrue(
			$hasError->invoke( null, $errors[ $line ], $sniffCode, $message ),
			sprintf(
				'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
				$sniffCode,
				$message !== null
					? sprintf( ' with message "%s"', $message )
					: '',
				$line,
				PHP_EOL . PHP_EOL,
				$line,
				PHP_EOL,
				$getFormattedErrors->invoke( null, $errors[ $line ] ),
				PHP_EOL
			)
		);

	}

}
