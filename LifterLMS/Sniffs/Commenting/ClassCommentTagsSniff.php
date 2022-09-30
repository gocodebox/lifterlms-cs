<?php

/**
 * ClassCommentTagsSniff class file.
 *
 * @package LifterLMSCS\LifterLMS\Sniffs\Commenting
 *
 * @since [version]
 * @version [version]
 */

declare( strict_types=1 );

namespace LifterLMSCS\LifterLMS\Sniffs\Commenting;

use PHP_CodeSniffer\Util\Tokens;

/**
 * Parses and verifies the class doc comment contains the configured tags / tag groups.
 *
 * @since [version]
 */
class ClassCommentTagsSniff extends AbstractCommentTags {

	/**
	 * List of the expected tag groups and their order within each group.
	 *
	 * @var string[]
	 */
	public array $groups = [
		'*@since... @deprecated',
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since [version]
	 *
	 * @return array
	 */
	public function register(): array {
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
			T_ENUM,
		];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the current token
	 *                                                in the stack passed in $tokens.
	 */
	public function process( $phpcs_file, $stack_ptr ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint, SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint, SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint

		$tokens               = $phpcs_file->getTokens();
		$find                 = Tokens::$methodPrefixes; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$find[ T_WHITESPACE ] = T_WHITESPACE;

		$prev_content = null;
		for ( $comment_end = $stack_ptr - 1; $comment_end >= 0; $comment_end-- ) {

			if ( isset( $find[ $tokens[ $comment_end ]['code'] ] ) ) {
				continue;
			}

			if ( null === $prev_content ) {
				$prev_content = $comment_end;
			}

			if (
				T_ATTRIBUTE_END === $tokens[ $comment_end ]['code'] &&
				isset( $tokens[ $comment_end ]['attribute_opener'] )
			) {
				$comment_end = $tokens[ $comment_end ]['attribute_opener'];
				continue;
			}

			break;
		}

		$comment_start = $tokens[ $comment_end ]['comment_opener'] ?? false;
		if ( ! $comment_start ) {
			return;
		}
		$this->process_tag_group( $phpcs_file, $comment_start );

	}

}
