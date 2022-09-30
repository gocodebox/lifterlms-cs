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

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Parses and verifies the class doc comment contains the configured tags / tag groups.
 *
 * @since [version]
 */
abstract class AbstractCommentTags implements Sniff {

	/**
	 * Error code: Multiple occurrences found for a non-list tag.
	 */
	public const CODE_DUPLICATE = 'TagDuplicate';

	/**
	 * Error code: Allowed extra tag found in the wrong group.
	 */
	public const CODE_EXTRA_WRONG_GROUP = 'ExtraTagInWrongGroup';

	/**
	 * Error code: Invalid tag found.
	 */
	public const CODE_INVALID = 'InvalidTag';

	/**
	 * Error code: Required tag not found.
	 */
	public const CODE_MISSING = 'TagMissing';

	/**
	 * Error code: Valid tag found in the wrong order.
	 */
	public const CODE_ORDER = 'TagOrder';

	/**
	 * Error code: Valid tag found in the wrong group.
	 */
	public const CODE_VALID_WRONG_GROUP = 'ValidTagInWrongGroup';

	/**
	 * Whether or not extra tags (not defined in $groups) are allowed.
	 *
	 * If `true`, any extra tags should be found in their own group after
	 * the defined groups.
	 *
	 * @var boolean
	 */
	public bool $allowExtraTags = false;

	/**
	 * List of the expected tag groups and their order within each group.
	 *
	 * This array should be an array strings and each string is a space-separated list of
	 * the tags to be found in the group.
	 *
	 * Each group must be separated by an empty line. If a tag is defined in the group it
	 * is required.
	 *
	 * Any tags that are not listed are considered invalid unless the `$allowExtraTags`
	 * property is `true`.
	 *
	 * @var string[]
	 */
	public array $groups = [];

	/**
	 * An array of tokens to ignore.
	 *
	 * @var string[]
	 */
	protected array $ignored_tokens = [
		T_DOC_COMMENT_WHITESPACE,
		T_DOC_COMMENT_STAR,
		T_DOC_COMMENT_STRING,
	];

	/**
	 * A list of tags denoted as "lists" via the `...` notation in the group definition.
	 *
	 * @var array
	 */
	protected $list_tags = [];

	/**
	 * Find the expected index of the tag group where the supplied tag belongs.
	 *
	 * @since [version]
	 *
	 * @param array[] $groups   Array of tag group arrays.
	 * @param string  $tag_name The tag name.
	 * @return int|bool Returns the index of the expected tag group or `false` if not found.
	 */
	protected function find_expected_tag_group_for_tag( array $groups, string $tag_name ): int|bool {

		foreach ( $groups as $groupIndex => $group ) {
			if ( in_array( $tag_name, $group, true ) ) {
				return $groupIndex;
			}
		}

		return false;

	}

	/**
	 * Flattens an array of tag groups to a list of tags contained within all tag groups.
	 *
	 * @since [version]
	 *
	 * @param array[] $groups Array of tag group arrays.
	 * @return string[]
	 */
	protected function flatten_groups( array $groups ): array {
		$flat = [];
		foreach ( $groups as $group ) {
			$flat = array_merge( $flat, $group );
		}
		return $flat;
	}

	/**
	 * Retrieves an array of expected tag group arrays.
	 *
	 * @since [version]
	 *
	 * @return array[]
	 */
	protected function get_expected_tags(): array {

		return array_map(
			function( string $group ): array {

				$tags = [];
				foreach ( explode( ' ', $group ) as $tag ) {
					$required     = 0 === strpos( $tag, '*' );
					$tag          = $required ? substr( $tag, 1 ) : $tag;
					if ( '...' === substr( $tag, -3 ) ) {
						$tag = substr( $tag, 0, strlen( $tag ) - 3 );
						$this->list_tags[] = $tag;
					}

					$tags[ $tag ] = $required;
				}
				return $tags;

			},
			$this->groups
		);

	}

	/**
	 * Determines if the tag group is required.
	 *
	 * If a tag group contains no required tags the entire group *may* be omitted.
	 *
	 * @since [version]
	 *
	 * @param array $group Tag group array.
	 * @return boolean Returns `true` if the group is required and `false` if it is not required.
	 */
	protected function is_tag_group_required( array $group ): bool {
		$vals = array_unique( array_values( $group ) );
		return in_array( true, $vals, true );
	}

	/**
	 * Process a tag group for a given comment.
	 *
	 * @since [version]
	 *
	 * @see [Reference]
	 * @link [URL]
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcs_file    The file being scanned.
	 * @param int                         $comment_start Position where the comment starts.
	 * @return void
	 */
	protected function process_tag_group( $phpcs_file, $comment_start ): void {

		$tokens = $phpcs_file->getTokens();

		$expected              = $this->get_expected_tags();
		$valid_tags            = $this->flatten_groups( $expected );
		$actual                = [];
		$found_tags            = [];
		$total_expected_groups = count( $expected );
		$currGroupIndex        = 0;
		$tag_counts            = [];

		foreach ( $tokens[ $comment_start ]['comment_tags'] as $tagIndex => $tag ) {

			// Current tag name.
			$name = $tokens[ $tag ]['content'];

			// Count tag occurrences.
			if ( empty( $tag_counts[ $name ] ) ) {
				$tag_counts[ $name ] = 0;
			}
			++$tag_counts[ $name ];

			// Setup an actual representation of the existing tag groups.
			$actual[ $currGroupIndex ][] = $name;

			// Setup the current expected and actual groups.
			$currGroup       = $expected[ $currGroupIndex ] ?? array();
			$actualCurrGroup = $actual[ $currGroupIndex ] ?? array();

			// Check if the next tag is in the same group as the current tag.
			$nextTag = $phpcs_file->findNext( $this->ignored_tokens, $tag + 1, null, true );
			if ( 1 !== $tokens[ $nextTag ]['line'] - $tokens[ $tag ]['line'] ) {
				$currGroupIndex++;
			}

			$isValidTag = in_array( $name, array_keys( $valid_tags ), true );

			// If extra tags are allowed, the tag isn't a valid predefined tag, and this the expected extra tag group, we can skip remaining checks.
			if ( $this->allowExtraTags && ! $isValidTag && $total_expected_groups === $currGroupIndex ) {
				continue;
			}

			$found_tags[] = $name;

			// The tag is invalid or in the incorrect group.
			if ( ! in_array( $name, array_keys( $currGroup ), true ) ) {

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
						$this->find_expected_tag_group_for_tag( $expected, $name )
					);
				} elseif ( $this->allowExtraTags ) {
					$code = self::CODE_EXTRA_WRONG_GROUP;
					$msg  =  $base . sprintf(
						'; Extra tags should be in the tag group at position %1$d',
						$total_expected_groups
					);
				}

				$phpcs_file->addError( $msg, $tag, $code );
			}

			$curr_group_tags       = array_keys( $currGroup );
			$expected_pos_in_group = array_search( $name, $curr_group_tags, true );
			$actual_pos_in_group   = array_search( $name, $actualCurrGroup, true );
			$previous_tag          = ! $expected_pos_in_group ? false : $curr_group_tags[ $expected_pos_in_group - 1 ];
			$previous_tag_optional = $previous_tag ? ! $currGroup[ $previous_tag ] : null;

			if ( $isValidTag && $expected_pos_in_group !== $actual_pos_in_group && ! $previous_tag_optional && ! in_array( $previous_tag, $this->list_tags, true ) ) {
				$phpcs_file->addError(
					sprintf(
						'The tag "%1$s" was found in position %2$d but should be in position %3$d',
						$name,
						$actual_pos_in_group,
						$expected_pos_in_group
					),
					$tag,
					self::CODE_ORDER
				);
			}

			if ( $isValidTag && $tag_counts[ $name ] > 1 && ! in_array( $name, $this->list_tags, true ) ) {
				$phpcs_file->addError(
					sprintf(
						'The tag "%1$s" may only be declared once',
						$name,
					),
					$tag,
					self::CODE_DUPLICATE
				);
			}
		}

		// Throw errors for any missing tags.
		foreach ( array_diff( array_keys( $valid_tags ), $found_tags ) as $missingTag ) {

			// Optional tag, continue without it.
			if ( false === $valid_tags[ $missingTag ] ) {
				continue;
			}

			$phpcs_file->addError(
				sprintf(
					'Missing tag "%1$s"; The tag should be present in the tag group at position %2$d',
					$missingTag,
					$this->find_expected_tag_group_for_tag( $expected, $missingTag )
				),
				$comment_start,
				self::CODE_MISSING
			);
		}

	}

}
