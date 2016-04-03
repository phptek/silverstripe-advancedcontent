# SilverStripe Advanced Content

Let's face it, existing solutions to structured content-editing in SilverStripe suck; Attempting to impose structure into a simple database text-field using JSON, serialiisation or markup just isn't the best way to structure content. You cannot share that content with other content objects without changes in one, affecting another.

## Compatibility

TBC but probably PHP5.3+
TBC but probably SilverStripe 3.1+

## Installation

  1) Git Clone


    #> git clone https://github.com/phptek/silverstripe-advancedcontent.git

  2) Composer command

    composer require phptek/silverstripe-advancedcontent dev-master

  3) Composer (Manual)

Edit your project's `composer.json` as follows:

Add a new line under the "require" block:

    deviateltd/silverstripe-advancedcontent

Add a new block under the "repositories" block:


      {
       "type": "vcs",
       "url": "https://github.com/phptek/silverstripe-advancedcontent.git"
      }

Now run `dev/build``via your browser or command line - and don't forget to flush.

