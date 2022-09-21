LifterLMS "Modern" PHP Coding Standard======================================<!-- These docs were automatically generated from the LifterLMS-Modern/ruleset.xml file on 2022-09-21T19:35:52Z. -->+ [1. Overview](#1-overview)+ [2. General](#2-general)  + [2.1. Basic Coding Standard](#21-basic-coding-standard)  + [2.2. Lines](#22-lines)  + [2.3. Indentation](#23-indentation)  + [2.4. PHP Tags](#24-php-tags)  + [2.5. Keyword and Type Capitalization](#25-keyword-and-type-capitalization)+ [3. Files](#3-files)  + [3.1. File Names](#31-file-names)  + [3.2. Character Encoding](#32-character-encoding)  + [3.3. File Header](#33-file-header)  + [3.4. File Comment](#34-file-comment)  + [3.4. End of File](#34-end-of-file)  + [3.5. Side Effects](#35-side-effects)  + [3.6. One Object Structure Per File](#36-one-object-structure-per-file)+ [4. Strict Typing and Type Hints](#4-strict-typing-and-type-hints)+ [5. Classes, Properties, and Methods](#5-classes,-properties,-and-methods)  + [5.1. Namespace and Class Names](#51-namespace-and-class-names)  + [5.2. Class Structure](#52-class-structure)  + [5.3. Class Spacing](#53-class-spacing)  + [5.4. Constants](#54-constants)  + [5.5. Properties](#55-properties)  + [5.6. Methods](#56-methods)+ [6. Functions](#6-functions)+ [7. Arrays](#7-arrays)  + [7.1. Short Array Syntax](#71-short-array-syntax)  + [7.2. Implicit Array Creation](#72-implicit-array-creation)  + [7.3. White Space and Indentation](#73-white-space-and-indentation)  + [7.4. Trailing Comma](#74-trailing-comma)+ [8. Constants](#8-constants)+ [9. Operators](#9-operators)  + [9.1. Null Coalesce](#91-null-coalesce)+ [10. Best Practices and Code Quality](#10-best-practices-and-code-quality)  + [10.1. Useless and Unused Code](#101-useless-and-unused-code)## 1. OverviewStandard Name: `LifterLMS-Modern`

PHPCS Ruleset File: [ruleset.xml](https://github.com/gocodebox/lifterlms-cs/LifterLMS-Modern/ruleset.xml)

This standards exists for new LifterLMS add-ons which opt-in to the modern standard.

The goal of the modern standard is to require usage of less-archaic (though not necessarily bleeding edge) PHP
code which are not found in the WordPress core coding standards and may be uncommon for many WordPress plugins
and themes.

Our modern projects require PHP 7.4 or later and utilize namepsaces, [PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/),
strict typing, short array syntax, and other language features as described below.## 2. General### 2.1. Basic Coding StandardCode must follow rules outlined in the WordPress Coding Standard.

Code MUST follow the rules outlined in the[WordPress PHP Coding Standard](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/),
with the following exceptions:+ File names should follow PSR-4 autoloading standards+ The Short Array syntax MUST be used.+ Classnames MUST NOT follow the default WordPress naming conventions and should### 2.2. LinesThere MUST NOT be a hard limit on line length.

The soft limit on line length must be 120 characters.

Lines SHOULD NOT be longer than 80 characters; lines longer than 80 characters SHOULD be split into multiple
subsequent lines of no more than 80 characters each.All PHP files MUST use the Unix LF (linefeed) line ending only.There MUST NOT be trailing whitespace at the end of lines.

Blank lines MAY be added to improve readability and to indicate related blocks of code except where explicitly
forbidden.There MUST NOT be more than one statement per line.### 2.3. IndentationIndentation should always reflect a logical structure.At the beginning of a line, PHP Code MUST use a **real tab** for indentation, and MUST NOT use spaces for
indenting.

Spaces MUST be used for mid-line alignment, and tabs MUST NOT be used for mid-line alignment.### 2.4. PHP TagsPHP code MUST use the long `<?php ?>` tags; it MUST NOT use the other tag variations.### 2.5. Keyword and Type CapitalizationPHP keywords and types MUST be in lower case.The PHP constants `true`, `false`, and `null` MUST be in lower case.Short form of type keywords MUST be used; i.e. `bool` instead of `boolean`, `int` instead of `integer` etc.## 3. Files### 3.1. File NamesPHP File names MUST adhere to the [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/) standard.### 3.2. Character EncodingPHP code MUST use only UTF-8 without BOM.### 3.3. File HeaderThe header of a PHP file may consist of a number of different blocks. If present, each of the blocks below MUST
be separated by a single blank line, and MUST NOT contain a blank line. Each block MUST be in the order listed
below, although blocks that are not relevant may be omitted.

+ Opening php tag.
+ File-level docblock.
+ One or more declare statements.
+ The namespace declaration of the file.
+ One or more class-based use import statements.
+ One or more function-based use import statements.
+ One or more constant-based use import statements.
+ The remainder of the code in the file.Use import statements SHOULD be alphabetically sorted and ordered by classes, functions, and constants.### 3.4. File CommentThe file comment MUST include a short description, an optional long description, an `@package` tag, and a
changelog tag group.

The changelog tag group MUST contain an `@since` tag which details the version when the file was
introduced and an `@version` tag detailing the current version of the file.

Each section of the file comment should be separated by a single line.

Example:
```php
/**
 * File short description
 *
 * An optional file long description.
 *
 * @package Vendor\Package
 *
 * @since 1.2.3
 * @version 4.5.6
 */
```

The file short description MUST start with a capital letter and MUST NOT end with a full-stop.

If included, the file long description MUST start with a capital letter and must end with a full-stop.

The `@package` tag SHOULD match the file's namespace.### 3.4. End of FileAll PHP files MUST end with a single blank line.The closing `?>` tag MUST be omitted from files containing only PHP.### 3.5. Side EffectsA file SHOULD declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it
SHOULD execute logic with side effects, but SHOULD NOT do both.### 3.6. One Object Structure Per FileA file containing an object structure (e.g. a class, trait, interface, etc...) MUST contain only one single
object structure.## 4. Strict Typing and Type HintsPHP files MUST use strict typing.Native type hints MUST be used for class property declarations.

A function or class method with a void return should not include a useless @return docblock.Native type hints MUST be used for function and class method parameter declarations.Native type hints MUST be used for function and class method return declarations.Union type hints SHOULD be used in favor of mixed.Type hints should not include spaces between pipe characters.

The shorthand question mark character for nullable types should be used in favor of `null|<type>`.When a return type declaration is present, there MUST be one space after the colon followed by the type
declaration. The colon and declaration MUST be on the same line as the argument list closing parenthesis with
no spaces between the two characters.## 5. Classes, Properties, and Methods### 5.1. Namespace and Class NamesClass names MUST be declared in PascalCase (or StudlyCaps), where where the first letter of each word is
capitalized including the very first letter.Namespaces and classes MUST follow PSR-0.

Each class is in a file by itself, and is in a namespace of at least one level: a top-level vendor name.### 5.2. Class StructureThe Structure of a class MUST match the following structure of class member groups:

1. Use import statements
2. Enum cases
3. Constants: private, protected, public
4. Static properties: private, protected, public
5. Properties: private, protected, public
6. Methods
  6a. Constructors
  6b. Destructor
  6c. Magic methods
  6d. Static abstract methods: protected, public
  6e. Abstract methods: protected, public
  6f. Static final methods: protected, public
  6g. Final methods: protected, public
  6h. Static methods: private, protected, public
  6i. Methods: private, protected, public

Items within each group SHOULD be alphabetized by member name.### 5.3. Class SpacingClass members, constants, properties, and methods MUST each be separated by a single empty line### 5.4. ConstantsThe visibility of class constants MUST be declared.### 5.5. PropertiesVisibility MUST be declared on all properties.

The var keyword MUST NOT be used to declare a property.

There MUST NOT be more than one property declared per statement.

Property names SHOULD NOT be prefixed with a single underscore to indicate protected or private visibility.### 5.6. MethodsThe visibility of methods MUST be declared.Method names SHOULD NOT be prefixed with a single underscore to indicate protected or private visibility.Method names MUST use lowercase letters separated by underscores and MUST NOT use camelCase.## 6. Functions## 7. Arrays### 7.1. Short Array SyntaxThe short array syntax MUST be used, and the long array syntax MUST NOT be used.### 7.2. Implicit Array CreationArrays must be explicitly created before being assigned values.### 7.3. White Space and IndentationWhen an associative array contains more than one item, each item array SHOULD start on a new line.The assignment double-arrows in an associative array SHOULD be aligned using mid-line space indentation.### 7.4. Trailing CommaA trailing comma SHOULD be included after the last item in a multi-line array.## 8. ConstantsConstants MUST be declared in all upper case with underscore separators.## 9. Operators### 9.1. Null CoalesceThe null coalesce `??` operator SHOULD be used in favor of a ternary whenever possible.## 10. Best Practices and Code Quality### 10.1. Useless and Unused CodeIf a class or function is imported it MUST be used in the file.Parenthesis should only be used when necessary.Importing from the same namespace is prohibited.