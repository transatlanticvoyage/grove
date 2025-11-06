<?php
/**
 * PAPYRUS VAULT 1
 * Sacred Text Repository - Grove Plugin Custodian
 * 
 * This vault contains the canonical papyrus-1 text.
 * Last Modified: <?php echo date('Y-m-d H:i:s'); ?>
 * Custodian: Grove Plugin Vault System
 * Classification: Static Sacred Text
 * Access: Cross-Plugin Available
 */

if (!defined('ABSPATH')) {
    exit; // Silence is golden
}

return <<<'PAPYRUS_TEXT'
==========================================================
BEGIN PAPYRUS
==========================================================
INTRODUCTION INSTRUCTIONS

Your job is to write the content for a specific page on my site for me.

This prompt will feed you information about the website itself and the specific page itself that you are creating.

The content you are creating will be injected into my custom plugin that will populate Elementor widgets with the content you have created.

Because of this, you must create the content in a special markup and follow special rules.


==========================================================
IMPORTANT TERMINOLOGY

Reference Example Model Content (REMC) - this is the content that I will be showing you to use as an example
New Live Created Content (NLCC) - this is the content you are creating for me in your output responses
Molecule Piece - this is an individual "piece" of content (each page we are creating will contain many molecules/molecule pieces) that is created to fit into a specific widget or item on the Elementor page



==========================================================
SITE-LEVEL INFO (from "flag 1 - ai" system)

/////////////////////////////////
[pappycode13 - INSERT HERE - MAIN SITE LEVEL DRIGGS DATA - flag1 ai chat for content generation]
/////////////////////////////////
[actual insert here]
/////////////////////////////////


==========================================================
SHORTCODE USAGE

Do not insert any statically-written content on the page when you would be writing the same DB values from these DB columns:

driggs_phone_1
driggs_email_1

Instead, please use the following respective shortcodes:

[phone_local]
[sitespren dbcol="driggs_email_1"]


==========================================================
PAGE-LEVEL PAGE-SPECIFIC INSTRUCTIONS

////////////////////////////////////////////
[pappycode14 - INSERT DB COLUMN VALUE FROM orbitposts.papyrus_page_level_insert]
////////////////////////////////////////////
[actual insert here]
////////////////////////////////////////////


==========================================================
ACTUAL SPECIFIC TOPIC TO COVER


Cover the same topic from the REMC (except for my specific website - obviously)
Cover a different topic (such as a different service page topic)


==========================================================
NOTE ABOUT SERVICES WIDGETS (IF ANY)

NoteToSelfKyle: this is important on homepage, etc.


==========================================================
HTML TAGS

You may only create the specific HTML tags in the NLCC that are specifically present in the REMC for that specific widget/item piece.

If a specific section in the REMC has no HTML tags in it, then do not place any HTML tags in the corresponding NLCC piece that you create. 

You do not have to create all the same HTML tags from the REMC in your corresponding NLCC piece, but you may not create HTML tags in our NLCC piece that are not already present in the REMC for that specific widget/item piece.

==========================================================
OUTBOUND LINKS (TO OTHER DOMAINS)
Do not create any outbound links. (even if the REMC contains them)
==========================================================
INNER LINKS (TO URLS ON OUR SAME DOMAIN)
Do not create any inner links. (even if the REMC contains them)
==========================================================
NOTES ON THE OUTPUT FORMATTING THAT I WANT (FOR YOU TO CREATE AND GIVE BACK TO ME)

———————————————————————
• place the output entirely within a codebox (with a 1 click copy button)
———————————————————————
• about the "guarded" class:


==========================================================
OTHER RANDOM NOTES (IF ANY)


==========================================================
END PAPYRUS
==========================================================
==========================================================
==========================================================
==========================================================
BEGIN REMC CONTENT BELOW (REFERENCE EXAMPLE MODEL CONTENT)
==========================================================





PAPYRUS_TEXT;