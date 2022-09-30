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
class FileCommentTagsSniffTest extends TestCase {

	/**
	 * Tests valid default config.
	 *
	 * @since [version]
	 */
	public function test_allowed_extra_tags(): void {
		$report = self::checkFileForTest( __METHOD__, __DIR__, [ 'allow_extra_tags' => true ] );
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

		self::assertSniffError( $report, 7, FileCommentTagsSniff::CODE_ORDER );
		self::assertSniffError( $report, 8, FileCommentTagsSniff::CODE_ORDER );

	}

	/**
	 * Tests missing package group.
	 *
	 * @since [version]
	 */
	public function test_missing_package_group(): void {

		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertSame( 5, $report->getErrorCount() );

		self::assertSniffError( $report, 2, FileCommentTagsSniff::CODE_MISSING );

	}

	/**
	 * Tests disallowed extra tags.
	 *
	 * @since [version]
	 */
	public function test_no_extra_tags(): void {

		$report = self::checkFileForTest( __METHOD__, __DIR__ );
		self::assertSame( 3, $report->getErrorCount() );

		self::assertSniffError( $report, 10, FileCommentTagsSniff::CODE_INVALID );
		self::assertSniffError( $report, 11, FileCommentTagsSniff::CODE_INVALID );
		self::assertSniffError( $report, 12, FileCommentTagsSniff::CODE_INVALID );

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

	/**
	 * Tests valid custom tag groups.
	 *
	 * @since [version]
	 */
	public function test_valid_custom_groups(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'*@test *@fake',
					'*@mocker',
					'*@additional *@moreCustom *@many',
				]
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests valid with optional tags.
	 *
	 * @since [version]
	 */
	public function test_valid_optional_tag_group(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'@optionalTag @alsoOptionalTag',
				]
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests valid with completely optional tag group mixed with required tags.
	 *
	 * @since [version]
	 */
	public function test_valid_optional_tag_group_with_required(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'*@package',
					'@optionalTag @alsoOptionalTag',
					'*@since',
				],
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests valid with completely optional tag group mixed with required tags and allowed extra tags.
	 *
	 * @since [version]
	 */
	public function test_valid_optional_tag_group_missing_with_allowed_extra_tags(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'*@package',
					'@optionalTag @alsoOptionalTag',
					'*@since',
				],
				'allow_extra_tags' => true,
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests valid with optional tags.
	 *
	 * @since [version]
	 */
	public function test_valid_optional_tags_missing(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'@optionalTag *@requiredTag',
				]
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

	/**
	 * Tests valid with optional tags.
	 *
	 * @since [version]
	 */
	public function test_valid_optional_tags_present(): void {
		$report = self::checkFileForTest(
			__METHOD__,
			__DIR__,
			[
				'groups' => [
					'@optionalTag *@requiredTag',
				]
			]
		);
		self::assertNoSniffErrorInFile( $report );
	}

}
