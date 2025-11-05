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
INTRODUCTION INSTRUCTIONS


==========================================================
SITE-LEVEL INFO (from "flag 1 - ai" system)

(INSERT HERE - MAIN SITE LEVEL DRIGGS DATA - flag1 ai chat for content generation)



==========================================================
SHORTCODE USAGE

Do not insert any static info


==========================================================
PAGE-LEVEL INFO

NoteToSelf: need somewhere in the orbitposts db table (in ruplin plugin) to create something for this

////////////////////////////////////////////
[INSERT DB COLUMN VALUE FROM orbit_posts]
////////////////////////////////////////////




==========================================================
NOTES ON THE OUTPUT FORMATTING THAT I WANT (FOR YOU TO CREATE AND GIVE BACK TO ME)

———————————————————————
• place the output entirely within a codebox (with a 1 click copy button)
———————————————————————
• about the "guarded" class:


==========================================================
OTHER RANDOM NOTES (IF ANY)


==========================================================
BEGIN ACTUAL CONTENT BELOW
==========================================================




PAPYRUS_TEXT;