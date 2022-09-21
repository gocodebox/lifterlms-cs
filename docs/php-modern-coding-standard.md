LifterLMS "Modern" PHP Coding Standard======================================<!-- These docs were automatically generated from the LifterLMS-Modern/ruleset.xml file on 2022-09-21T00:18:36Z. -->## Contents+ [1. Overview](#1-overview)+ [2. General](#2-general)+ [2.1. Basic Coding Standard](#21-basic-coding-standard)+ [2.2. Files](#22-files)+ [2.2.1. File names](#221-file-names)+ [2.2.2. Start of file](#222-start-of-file)+ [2.2.3. End of file](#223-end-of-file)+ [2.3. Lines](#23-lines)+ [2.4. Indentation](#24-indentation)+ [2.5. Character Encoding](#25-character-encoding)+ [2.6. PHP Tags](#26-php-tags)+ [2.7. Keyword and Type Capitalization](#27-keyword-and-type-capitalization)+ [2.8. Side Effects](#28-side-effects)+ [3. Strict Typing and Type Hints](#3-strict-typing-and-type-hints)+ [4. 3 Class Spacing](#4-3-class-spacing)+ [4.4. Constants](#44-constants)+ [4.5. Properties](#45-properties)+ [4.6. Methods](#46-methods)+ [5. Functions](#5-functions)+ [6. 4 Trailing Comma](#6-4-trailing-comma)+ [6.1. Short Array Syntax](#61-short-array-syntax)+ [6.2. Implicit Array Creation](#62-implicit-array-creation)+ [6.3. White Space and Indentation](#63-white-space-and-indentation)## 1. OverviewStandard Name: `LifterLMS-Modern`

PHPCS Ruleset File: [ruleset.xml](https://github.com/gocodebox/lifterlms-cs/LifterLMS-Modern/ruleset.xml)

This standards exists for new LifterLMS add-ons which opt-in to the modern standard.

The goal of the modern standard is to require usage of less-archaic (though not necessarily bleeding edge) PHP
code which are not found in the WordPress core coding standards and may be uncommon for many WordPress plugins
and themes.

Our modern projects require PHP 7.4 or later and utilize namepsaces, [PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/),
strict typing, short array syntax, and other language features as described below.## 2. General### 2.1. Basic Coding StandardCode must follow rules outlined in the WordPress Coding Standard.

Code MUST follow the rules outlined in the[WordPress PHP Coding Standard](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/),
with the following exceptions:+ File names should follow PSR-4 autoloading standards+ The Short Array syntax MUST be used.### 2.2. Files#### 2.2.1. File namesPHP File names MUST adhere to the [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/) standard.#### 2.2.2. Start of fileThe header of a PHP file may consist of a number of different blocks. If present, each of the blocks below MUST
be separated by a single blank line, and MUST NOT contain a blank line. Each block MUST be in the order listed
below, although blocks that are not relevant may be omitted.

+ Opening php tag.
+ File-level docblock.
+ One or more declare statements.
+ The namespace declaration of the file.
+ One or more class-based use import statements.
+ One or more function-based use import statements.
+ One or more constant-based use import statements.
+ The remainder of the code in the file.Use import statements SHOULD be alphabetically sorted and ordered by classes, functions, and constants.#### 2.2.3. End of fileAll PHP files MUST end with a single blank line.The closing `?>` tag MUST be omitted from files containing only PHP.### 2.3. LinesThere MUST NOT be a hard limit on line length.

The soft limit on line length must be 120 characters.

Lines SHOULD NOT be longer than 80 characters; lines longer than 80 characters SHOULD be split into multiple
subsequent lines of no more than 80 characters each.All PHP files MUST use the Unix LF (linefeed) line ending only.There MUST NOT be trailing whitespace at the end of lines.

Blank lines MAY be added to improve readability and to indicate related blocks of code except where explicitly
forbidden.There MUST NOT be more than one statement per line.### 2.4. IndentationIndentation should always reflect a logical structure.At the beginning of a line, PHP Code MUST use a **real tab** for indentation, and MUST NOT use spaces for
indenting.

Spaces MUST be used for mid-line alignment, and tabs MUST NOT be used for mid-line alignment.### 2.5. Character EncodingPHP code MUST use only UTF-8 without BOM.### 2.6. PHP TagsPHP code MUST use the long `<?php ?>` tags; it MUST NOT use the other tag variations.### 2.7. Keyword and Type CapitalizationPHP keywords and types MUST be in lower case.The PHP constants `true`, `false`, and `null` MUST be in lower case.Short form of type keywords MUST be used; i.e. `bool` instead of `boolean`, `int` instead of `integer` etc.### 2.8. Side EffectsA file SHOULD declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it
SHOULD execute logic with side effects, but SHOULD NOT do both.## 3. Strict Typing and Type HintsPHP files MUST use strict typing.Native type hints MUST be used for class property declarations.

A function or class method with a void return should not include a useless @return docblock.Native type hints MUST be used for function and class method parameter declarations.Native type hints MUST be used for function and class method return declarations.Union type hints SHOULD be used in favor of mixed.Type hints should not include spaces between pipe characters.

The shorthand question mark character for nullable types should be used in favor of `null|<type>`.When a return type declaration is present, there MUST be one space after the colon followed by the type
declaration. The colon and declaration MUST be on the same line as the argument list closing parenthesis with
no spaces between the two characters.## 4. Classes, Properties, and Methods## 4.1 Namespace and Class NamesClass names MUST be declared in PascalCase (or StudlyCaps), where where the first letter of each word is
capitalized including the very first letter.Namespaces and classes MUST follow PSR-0.

Each class is in a file by itself, and is in a namespace of at least one level: a top-level vendor name.## 4.2 Class StructureThe Structure of a class MUST match the following structure of class member groups:

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

Items within each group SHOULD be alphabetized by member name.## 4.3 Class SpacingClass members, constants, properties, and methods MUST each be separated by a single empty line### 4.4. ConstantsThe visibility of class constants MUST be declared.### 4.5. PropertiesVisibility MUST be declared on all properties.

The var keyword MUST NOT be used to declare a property.

There MUST NOT be more than one property declared per statement.

Property names SHOULD NOT be prefixed with a single underscore to indicate protected or private visibility.### 4.6. MethodsThe visibility of methods MUST be declared.Method names SHOULD NOT be prefixed with a single underscore to indicate protected or private visibility.Method names MUST use lowercase letters separated by underscores and MUST NOT use camelCase.## 5. Functions## 6. Arrays### 6.1. Short Array SyntaxThe short array syntax MUST be used, and the long array syntax MUST NOT be used.### 6.2. Implicit Array CreationArrays must be explicitly created before being assigned values.### 6.3. White Space and IndentationWhen an associative array contains more than one item, each item array SHOULD start on a new line.The assignment double-arrows in an associative array SHOULD be aligned using mid-line space indentation.## 6.4 Trailing CommaA trailing comma SHOULD be included after the last item in a multi-line array.Constants MUST be declared in all upper case with underscore separators.The null coalesce "??" operator should be used in favor of ternary whenvere possible.If a class or function is imported it MUST be used in the file.Importing from the same namespace is prohibited.Parenthesis should only be used when necessary.<rule ref="Squiz.Commenting.ClassComment" /><property name="allowedTags" type="array">
<element value="@package" />
<element value="@since" />
</property>