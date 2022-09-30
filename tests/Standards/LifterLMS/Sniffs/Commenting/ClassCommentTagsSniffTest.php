<?php

/**
 * FileCommentTagsSniffTest class file.
 *
 * @package LifterLMSCS\LifterLMS\Sniffs\Commenting
 *
 * @since [version]
 * @version [version]
 */

declare( strict_types=1 );

namespace LifterLMSCS\LifterLMS\Sniffs\Commenting;

use LifterLMSCS\LifterLMS\Sniffs\TestCase;

/**
 * Tests the FileCommentTagsSniff class.
 *
 * @since [version]
 */
class ClassCommentTagsSniffTest extends TestCase {

	/**
	 * Tests invalid class comments.
	 *
	 * @since [version]
	 */
	public function test_invalid(): void {
		$report = self::checkFileForTest( __METHOD__, __DIR__ );

		self::assertSame( 5, $report->getErrorCount() );

		// A.
		self::assertSniffError( $report, 3, FileCommentTagsSniff::CODE_MISSING );

		// B.
		self::assertSniffError( $report, 13, FileCommentTagsSniff::CODE_DUPLICATE );

		// C.
		self::assertSniffError( $report, 21, FileCommentTagsSniff::CODE_ORDER );
		self::assertSniffError( $report, 22, FileCommentTagsSniff::CODE_ORDER );

		// D.
		self::assertSniffError( $report, 32, FileCommentTagsSniff::CODE_INVALID );

	}

	/**
	 * Tests valid class comments.
	 *
	 * @since [version]
	 */
	public function test_valid(): void {
		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertNoSniffErrorInFile( $report );
	}

}
