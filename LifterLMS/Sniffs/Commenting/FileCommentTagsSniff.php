<?php

namespace LifterLMSCS\LifterLMS\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;

/**
 * Customizes the default Squiz_FileCommentSniff to match tags
 * in the order specified by LifterLMS documentation standards
 *
 * @link https://github.com/gocodebox/lifterlms/blob/master/docs/documentation-standards.md#file-headers
 */
class FileCommentTagsSniff extends AbstractCommentTags {

	/**
	 * List of the expected tag groups and their order within each group.
	 *
	 * @var string[]
	 */
	public array $groups = [
		'*@package',
		'*@since *@version',
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array<int, (int|string)>
	 */
	public function register(): array {
		return [ T_OPEN_TAG ];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file The file being scanned.
	 * @param int                         $stack_ptr  The position of the current token
	 *                                                in the stack passed in $tokens.
	 * @return int Stack pointer to skip the rest of the file.
	 */
	public function process( File $phpcs_file, int $stack_ptr ): int {

		$tokens        = $phpcs_file->getTokens();
		$comment_start = $phpcs_file->findNext( T_WHITESPACE, $stack_ptr + 1, null, true );

		// Don't process an unfinished file comment during live coding.
		if (
			isset( $tokens[ $comment_start ]['comment_closer'] ) === false ||
			(
				$tokens[ $tokens[ $comment_start ]['comment_closer'] ]['content'] === '' &&
				$tokens[ $comment_start ]['comment_closer'] === $phpcs_file->numTokens - 1
			)
		) {
			return $phpcs_file->numTokens + 1;
		}

		$this->process_tag_group( $phpcs_file, $comment_start );

		// Ignore the rest of the file.
		return $phpcs_file->numTokens + 1;

	}
}
