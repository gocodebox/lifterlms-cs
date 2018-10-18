LifterLMS Coding Standards
==========================

LifterLMS Coding Standards (LifterLMS-CS) is a project with rulesets for code style and quality intended for use in LifterLMS projects.


## Installation

To include in a project require as a development dependency:

`composer require lifterlms/lifterlms-cs:dev-master --dev`

Note that Composer won't run configuration scripts in this scenario and the root project needs to take care of it.

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