<?php

declare( strict_types=1 );

namespace LifterLMSCS\LifterLMS\Sniffs\Commenting;

use LifterLMSCS\LifterLMS\Sniffs\TestCase;

class FileCommentSniffTest extends TestCase {

	/**
	 * Tests valid default config.
	 *
	 * @since [version]
	 */
	public function test_allowed_extra_tags(): void {
		$report = self::checkFileForTest( __METHOD__, __DIR__, [ 'allowExtraTags' => true ] );
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests incorrect order.
	 *
	 * @since [version]
	 */
	public function test_incorrect_order(): void {

		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertSame( 2, $report->getErrorCount() );

		self::assertSniffError( $report, 7, FileCommentSniff::CODE_ORDER );
		self::assertSniffError( $report, 8, FileCommentSniff::CODE_ORDER );

	}

	/**
	 * Tests missing package group.
	 *
	 * @since [version]
	 */
	public function test_missing_package_group(): void {

		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertSame( 5, $report->getErrorCount() );

		self::assertSniffError( $report, 2, FileCommentSniff::CODE_MISSING );

	}

	/**
	 * Tests disallowed extra tags.
	 *
	 * @since [version]
	 */
	public function test_no_extra_tags(): void {

		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertSame( 3, $report->getErrorCount() );

		self::assertSniffError( $report, 10, FileCommentSniff::CODE_INVALID );
		self::assertSniffError( $report, 11, FileCommentSniff::CODE_INVALID );
		self::assertSniffError( $report, 12, FileCommentSniff::CODE_INVALID );

	}

	/**
	 * Tests valid default config.
	 *
	 * @since [version]
	 */
	public function test_valid(): void {
		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertNoSniffErrorInFile( $report );
	}

}
