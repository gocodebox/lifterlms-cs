<?php

/**
 * Valid comment with single @since tag.
 *
 * @since 1.2.3
 */
class A {}

/**
 * Valid comment with single @since tag and a @deprecated tag.
 *
 * @since 1.2.3
 * @deprecated 4.5.6 Deprecated description.
 */
class B {}

/**
 * Valid comment with multiple @since tags.
 *
 * @since 1.2.3
 * @since 4.5.6 A description.
 * @since 7.8.9 A description.
 */
class C {}

/**
 * Valid comment with multiple @since tags and a @deprecated tag.
 *
 * @since 1.2.3
 * @since 4.5.6 A description.
 * @deprecated 7.8.9 Deprecated description.

 */
class D {}

/**
 * Valid comment with single @since tag.
 *
 * @since 1.2.3
 */
abstract class E {}

/**
 * Valid comment with single @since tag and a @deprecated tag.
 *
 * @since 1.2.3
 * @deprecated 4.5.6 Deprecated description.
 */
trait F {}

/**
 * Valid comment with multiple @since tags.
 *
 * @since 1.2.3
 * @since 4.5.6 A description.
 * @since 7.8.9 A description.
 */
interface G {}

/**
 * Valid comment with multiple @since tags and a @deprecated tag.
 *
 * @since 1.2.3
 * @since 4.5.6 A description.
 * @deprecated 7.8.9 Deprecated description.

 */
enum H {}
