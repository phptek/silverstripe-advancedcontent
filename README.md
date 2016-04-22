## Introduction

As a Content Author, Information Architect or Developer, have you ever wanted to do any of these? Does does your current content-block solution provide you with the following?

* Drag and drop content re-ordering
* Auto generated table of contents like Google Docs
* Fine-grained permissions on content blocks
* Embargo and Expiry dates on content blocks
* RESTful API for individual block content in JSON, XML or plain text. Great for AJAX-oriented IA's
* Hide the WYSIWYG editor completely
* Write custom content blocks, based on a well-documented developer API
* Override content block templates
* Content blocks available in search results

## Goals

 1. A complete and viable alternative to 1990s era MS Word-centric content editing
 2. Performance parity with SilverStripe's out of the box Content field
 3. A complete developer API allowing for further content management advancements

## Compatibility

* PHP v5.4+
* SilverStripe framework v3.1+
* SilverStripe cms v3.1+

## Installation

  1) Git

    #> git clone https://github.com/phptek/silverstripe-advancedcontent.git

  2) Composer

    composer require phptek/silverstripe-advancedcontent dev-master

Now run `dev/build`via your browser or command line - and don't forget to flush or things will smell..

## Documentation

See the docs folder.

## Versioning

This module follows Semver. According to Semver, you will be able to upgrade to any minor or patch version of this module without any breaking changes to the public API. Semver also requires that we clearly define the public API for this module.

All methods, with public visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep protected methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.
