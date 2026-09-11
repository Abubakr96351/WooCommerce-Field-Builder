<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Field definitions are kept on uninstall so deactivating/reinstalling the
// plugin doesn't destroy store configuration. Delete them manually via
// Field Builder > Product Fields if a full removal is needed.
