# Elevator Pitch

Becuase forcing structure into blobs of text sucks.

# Introduction

As a Content Author or Information Architect, have you ever wanted to do any of these _without leaving the screen, or selecting another tab_:

* Drag and drop re-order content?
* Auto generate a contents list as skiplinks like Google Docs does?
* Add fine-grained permissions to individual blocks?
* Access individual blocks of content via a URL for a AJAX orientated IA? 
* Add Embargo and Expiry dates to individual blocks?

# Roadmap

* [MVP] CMS UI is a TinyMCE replacement optionally shown by ticking a checkbox telling SS to use TinyMCE or the module's content-blocks (The system could use TinyMCE for some pages, and objects for others) 
* [MVP] The module's UI is called "ACE" (Advanced Content Editor) 
* [MVP] ACE shows a "Block Manager" as a replacement for TinyMCE, looking very similar to AdvancedWorkflow's workspace. Available Blocks are: 
	- "File"
	- "Image"
	- "Text"
	- "Heading"
	- "External Content"
..all of which are shown in the Block Manager and are created ala Advanced Workflow via a dropdown list of Block types.
* [MVP] For pages generated using content-blocks, an automatically generated skiplink list ala Google Docs will be shown. This can be enabled using a checkbox via the Block Manager.
* [MVP] Each block when rendered in the frontend, will have a unique HTML ID for use in skiplinks.
* [MVP] Each block allows for permissions to govern who (Users = Groups) can edit it - ala Advanced Workflow's actions.
* [MVP] Once created, each block shows an appropriate icon.
* [MVP] Once created, each block is available to edit using standard SS UI form widgets from within a CMS modal.
* [MVP] Basic service-based API that allows developers to build arbitrary services on top of the module, to provide blocks with custom features. 
* [MVP] Versionable blocks
* [V2.0] Each micro-object can be tagged using free-text from within the same UI. This will help with future functionality to link / relate a page to existing micro-objects used elsewhere in the site.
* [V2.0] Module comes with basic CSS grid for arranging structured content "items" within the current page-template's $Content area. The latter being generated using renderWith(). 
* [V2.0] Within the workspace authors select a desired grid, then blocks can be arranged within the workspace and "snap" to the grid within the workspace, thus representing what the frontend will look like fairly closely.

## Compatibility

* PHP v5.4+
* SilverStripe framework v3.1+
* SilverStripe cms v3.1+

## Advantages

Advantages: 

* No mess: No fiddling around injecting templates into TinyMCE or adding endless CSS classes to appear in the editor's style dropdown.
* No code: No need to modify templates. Replaces the CMS' $Content template variable when enabled on a page-by-page basis.
* Table of contents: Optionally add a table of contents to your pages ala Google Docs.
* Optional: Stick to the default editor on some pages, enable advanced content on others.
* Drag and drop: Content block re-ordering.
* Flexibility: Create separate headings, content, image, file an 3rd party data blocks.
* Permission control: Fine grained author-control over who sees which block within the CMS.
* Multi-use: Out of the box support for advanced content on SiteTree and DataObject types.
* Embargo / Expiry: Show and hide each block based on embargo and expiry date.
* API: Simple developer API to create custom blocks without hacking.
* IA flexibility: Allow front-end devs to create flexible layouts by calling portions (blocks) of content via AJAX.

## Installation

  1) Git Clone

    #> git clone https://github.com/phptek/silverstripe-advancedcontent.git

  2) Composer command

    composer require phptek/silverstripe-advancedcontent dev-master

  3) Composer (Manual)

Edit your project's `composer.json` as follows:

Add a new line under the "require" block:

    phptek/silverstripe-advancedcontent

Add a new block under the "repositories" block:


      {
       "type": "vcs",
       "url": "https://github.com/phptek/silverstripe-advancedcontent.git"
      }

Now run `dev/build``via your browser or command line - and don't forget to flush or things will smell..

## Documentation

See the docs folder.

## Versioning

This module follows Semver. According to Semver, you will be able to upgrade to any minor or patch version of this module without any breaking changes to the public API. Semver also requires that we clearly define the public API for this module.

All methods, with public visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep protected methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.
