<?php
namespace LifterLMSCS\LifterLMS\Sniffs\Commenting;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\FileCommentSniff as SquizFileCommentSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Customizes the default Squiz_FileCommentSniff to match tags
 * in the order specified by LifterLMS documentation standards
 *
 * @link https://github.com/gocodebox/lifterlms/blob/master/docs/documentation-standards.md#file-headers
 */
class FileCommentSniff extends SquizFileCommentSniff {

	public const CODE_INVALID = 'InvalidTag';

	public const CODE_VALID_WRONG_GROUP = 'ValidTagInWrongGroup';

	public const CODE_EXTRA_WRONG_GROUP = 'ExtraTagInWrongGroup';

	public const CODE_ORDER = 'TagOrder';

	public const CODE_MISSING = 'TagMissing';

	/**
	 * List of the expected tag groups and their order within each group.
	 *
	 * @var string[]
	 */
	public $groups = [
		'@package',
		'@since @version',
	];

	/**
	 * Whether or not extra tags (not defined in $groups) are allowed.
	 *
	 * If `true`, any extra tags should be found in their own group after
	 * the defined groups.
	 *
	 * @var boolean
	 */
	public $allowExtraTags = false;

	protected function getExpectedTags() {

		return array_map(
			function( string $group ): array {
				return explode( ' ', $group );
			},
			$this->groups
		);

	}

	protected function flattenGroups( $groups ) {
		$flat = [];
		foreach ( $groups as $group ) {
			$flat = array_merge( $flat, $group );
		}
		return $flat;
	}

	protected function findExpectedTagGroupForTag( $groups, $tagName ) {

		foreach ( $groups as $groupIndex => $group ) {
			if ( in_array( $tagName, $group, true ) ) {
				return $groupIndex;
			}
		}

		return false;

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return int Stack pointer to skip the rest of the file.
	 */
	public function process( File $phpcsFile, $stackPtr ) {

		$tokens       = $phpcsFile->getTokens();
		$commentStart = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );

		// Don't process an unfinished file comment during live coding.
		if (
			isset( $tokens[ $commentStart ]['comment_closer']) === false ||
			(
				$tokens[ $tokens[ $commentStart ]['comment_closer']]['content'] === '' &&
				$tokens[ $commentStart ]['comment_closer'] === ( $phpcsFile->numTokens - 1 )
			)
		) {
			return ( $phpcsFile->numTokens + 1 );
		}

		$expected            = $this->getExpectedTags();
		$validTags           = $this->flattenGroups( $expected );
		$foundTags           = [];
		$totalExpectedGroups = count( $expected );

		$actual       = [];



		$currGroupIndex      = 0;



		$empty        = [
			T_DOC_COMMENT_WHITESPACE,
			T_DOC_COMMENT_STAR,
		];

		// $commentEnd = $tokens [ $commentStart ]['comment_closer'];

		foreach ( $tokens[ $commentStart ]['comment_tags'] as $tagIndex => $tag ) {

			// Current tag name.
			$name = $tokens[ $tag ]['content'];

			// Setup an actual representation of the existing tag groups.
			$actual[ $currGroupIndex ][] = $name;

			// Setup the current expected and actual groups.
			$currGroup       = $expected[ $currGroupIndex ] ?? array();
			$actualCurrGroup = $actual[ $currGroupIndex ] ?? array();

			// Check if the next tag is in the same group as the current tag.
			$nextTag = $phpcsFile->findNext( [ ...$empty, T_DOC_COMMENT_STRING ], $tag + 1, null, true );
			if ( 1 !== $tokens[ $nextTag ]['line'] - $tokens[ $tag ]['line'] ) {
				$currGroupIndex++;
			}

			$isValidTag = in_array( $name, $validTags, true );

			// If extra tags are allowed, the tag isn't a valid predefined tag, and this the expected extra tag group, we can skip remaining checks.
			if ( $this->allowExtraTags && ! $isValidTag && $totalExpectedGroups === $currGroupIndex ) {
				continue;
			}

			$foundTags[] = $name;

			// The tag is invalid or in the incorrect group.
			if ( ! in_array( $name, $currGroup, true ) ) {

				$code = self::CODE_INVALID;
				$msg  = sprintf(
					'The tag "%s" is invalid',
					$name,
				);
				$base = sprintf(
					'The tag "%1$s" should not be in the tag group at position %2$d',
					$name,
					$currGroupIndex
				);

				if ( $isValidTag ) {
					$code = self::CODE_VALID_WRONG_GROUP;
					$msg  = $base . sprintf(
						'; The tag should be in the tag group at position %1$d',
						$this->findExpectedTagGroupForTag( $expected, $name )
					);
				} elseif ( $this->allowExtraTags ) {
					$code = self::CODE_EXTRA_WRONG_GROUP;
					$msg  =  $base . sprintf(
						'; Extra tags should be in the tag group at position %1$d',
						$totalExpectedGroups
					);
				}

				$phpcsFile->addError( $msg, $tag, $code );
			}


			$expectedPositionInGroup = array_search( $name, $currGroup, true );
			$actualPositionInGroup   = array_search( $name, $actualCurrGroup, true );

			if ( $isValidTag && $expectedPositionInGroup !== $actualPositionInGroup ) {
				$phpcsFile->addError(
					sprintf(
						'The tag "%1$s" was found in position %2$d but should be in position %3$d',
						$name,
						$actualPositionInGroup,
						$expectedPositionInGroup
					),
					$tag,
					self::CODE_ORDER
				);
			}
		}

		// Throw errors for any missing tags.
		foreach ( array_diff( $validTags, $foundTags ) as $missingTag ) {

			$phpcsFile->addError(
				sprintf(
					'Missing tag "%1$s"; The tag should be present in the tag group at position %2$d',
					$missingTag,
					$this->findExpectedTagGroupForTag( $expected, $missingTag )
				),
				$commentStart,
				self::CODE_MISSING
			);
		}

		// Ignore the rest of the file.
		return ( $phpcsFile->numTokens + 1 );

	}
}
