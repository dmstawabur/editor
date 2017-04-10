[![Build Status](https://scrutinizer-ci.com/g/gplcart/editor/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/editor/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/editor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/editor/?branch=master)

Theme Editor is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows users with appropriate permissions to edit theme template, CSS and JS files right from UI.

Features:

- Can edit templates, CSS and JS files
- Validates TWIG syntax before saving
- Automatically makes a backup zip
- Access control
- Supports [Code Mirror](https://github.com/gplcart/codemirror)


**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/editor`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Go to `admin/user/role` and grant module permissions to the selected role. Be careful about `Theme editor: edit file` (key editor_edit) - it allows users to save edited files

Usage:

- Start from `admin/tool/editor`