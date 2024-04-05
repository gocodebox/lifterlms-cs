LifterLMS Coding Standards
==========================

LifterLMS Coding Standards (LifterLMS-CS) is a project with rulesets for code style and quality intended for use in LifterLMS projects.


## Installation

To include in a project require as a development dependency:

`composer require lifterlms/lifterlms-cs:dev-trunk --dev`

If you are upgrading from the old version, be sure to remove any references to manually setting the `installed_paths`as they are now automatically set by Composer.

## Using PHPCS & PHPCBF

Access the PHPCS execultable via: `./vendor/bin/phpcs`
Check for errors only: `./vendor/bin/phpcs --error-severity=1 --warning-severity=6`
Fix errors via PHPCBF: `./vendor/bin/phpcbf`

## Predefined scripts

The following scripts can be added to your `composer.json` file for easy access to thes scripts & to ensure configurations are automatically set during package installation and updates.

```json
"scripts": {
    "check-cs": [
        "\"vendor/bin/phpcs\" --colors"
    ],
    "check-cs-errors": [
        "\"vendor/bin/phpcs\" --colors --error-severity=1 --warning-severity=6"
    ],
    "fix-cs": [
        "\"vendor/bin/phpcbf\""
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
