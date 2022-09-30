LifterLMS Coding Standards
==========================

LifterLMS Coding Standards (LifterLMS-CS) is a project with rulesets for code style and quality intended for use in LifterLMS projects.


## Installation

To include in a project require as a development dependency:

`composer require lifterlms/lifterlms-cs:dev-master --dev`

Note that Composer won't run configuration scripts in this scenario and the root project needs to take care of it.

After installing setup configurations:

+ `./vendor/bin/phpcs --config-set installed_paths ../../../vendor/wp-coding-standards/wpcs,../../../vendor/lifterlms/lifterlms-cs,../../../vendor/phpcompatibility/php-compatibility,../../../vendor/phpcompatibility/phpcompatibility-paragonie,../../../vendor/phpcompatibility/phpcompatibility-wp`
+ `./vendor/bin/phpcs --config-set default_standard LifterLMS"`


## Using PHPCS & PHPCBF

Access the PHPCS execultable via: `./vendor/bin/phpcs`
Check for errors only: `./vendor/bin/phpcs --error-severity=1 --warning-severity=6`
Fix errors via PHPCBF: `./vendor/bin/phpcbf`


## Predefined scripts

The following scripts can be added to your `composer.json` file for easy access to thes scripts & to ensure configurations are automatically set during package installation and updates.

```json
"scripts": {
    "config-cs": [
        "\"vendor/bin/phpcs\" --config-set installed_paths ../../../vendor/wp-coding-standards/wpcs,../../../vendor/lifterlms/lifterlms-cs,../../../vendor/phpcompatibility/php-compatibility,../../../vendor/phpcompatibility/phpcompatibility-paragonie,../../../vendor/phpcompatibility/phpcompatibility-wp",
        "\"vendor/bin/phpcs\" --config-set default_standard LifterLMS"
    ],
    "check-cs": [
        "\"vendor/bin/phpcs\" --colors"
    ],
    "check-cs-errors": [
        "\"vendor/bin/phpcs\" --colors --error-severity=1 --warning-severity=6"
    ],
    "fix-cs": [
        "\"vendor/bin/phpcbf\""
    ],
    "post-install-cmd": [
        "composer config-cs"
    ],
    "post-update-cmd": [
        "composer config-cs"
    ]
}
```

## Developing LifterLMS-CS

To include in a project and make changes to the LifterLMS-CS project itself:

Add to your `composer.json` file with the `@dev` tag and reference your local copy of this repository in the repositories block:

```json
"require-dev": {
    "lifterlms/lifterlms-cs": "@dev"
},
"repositories": [
    {
        "type": "path",
        "url": "/absolute/path/to/lifterlms-cs"
    }
]
```

## Sniffs

### LifterLMS.Commenting.FileComment

Ensures that the configured tags are included in the file comment, in the correct order, and grouping.

#### Properties

##### groups

The `groups` property is an array of tag groups.

Each element in the array is a space-separated list of the required tags in the tag group. The sniff will validate that the tags are in the correct group and that the tags are in the correct order within the group.

Each group must be separated by a single blank line.

Example configuration:

```xml
  <rule ref="LifterLMS.Commenting.FileComment">
    <properties>
      <property name="groups" type="array">
        <element value="*@package" />
        <element value="*@since *@version" />
      </property>
    </properties>
  </rule>
```

Example valid header comment:

```php
/**
 * Header short description
 *
 * @package Package\Name
 *
 * @since 1.2.3
 * @version 4.5.6
 */
```

##### Group Tag Notation

When defining a group of tags, a shorthand notation can be used to define various tag options.

+ **REQUIRED TAG**: To denote a tag is *required*, prepend the `*` character to the tag, eg: `*@since`. By default, tags are optional unless explicitly defined as required.
+ **LISTABLE TAG**: A list tag is a tag that can be used multiple times. Append `...` to the tag to make the tag listable, eg: `@since...`. By default, tags may only be used once per comment unless explicitly defined as listable.

If a tag is marked as both required and listable, only the first occurrence of the tag is required, all subsequent occurrences are always optional.


##### allow_extra_tags

The `allow_extra_tags` property is a `boolean` property which determines whether or not extra custom tags can be added to the file comment.

When `true`, extra tags MUST be included in a separate tag group after the defined tag groups.

```xml
  <rule ref="LifterLMS.Commenting.FileComment">
    <properties>
      <property name="allow_extra_tags" value="true" />
    </properties>
  </rule>
```

Example valid header comment:

```php
/**
 * Header short description
 *
 * @package Package\Name
 *
 * @since 1.2.3
 * @version 4.5.6
 *
 * @anExtraTag
 */
```
