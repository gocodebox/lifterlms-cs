<?php

/**
 * Missing required tags.
 */
class A {}

/**
 * Deprecated can only be declared once.
 *
 * @since 1.2.3
 * @deprecated [version] A.
 * @deprecated [version] B.
 */
class B {}

/**
 * Wrong order.
 *
 * @deprecated [version] A.
 * @since 1.2.3
 * @since 4.5.6 A.
 */
class C {}

/**
 * Wrong order.
 *
 * @since 1.2.3
 * @deprecated [version] A.
 *
 * @extraTag
 */
class D {}
