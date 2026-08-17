/**
 * Load TinyMCE from npm (self-hosted) instead of Tiny Cloud.
 * Avoids monthly API-key quota; same editor UI and plugins.
 */
import 'tinymce'
import 'tinymce/icons/default/icons'
import 'tinymce/themes/silver'
import 'tinymce/models/dom'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/link'
import 'tinymce/plugins/image'
import 'tinymce/plugins/table'
import 'tinymce/plugins/code'
import 'tinymce/skins/ui/oxide/skin.min.css'

export const TINYMCE_LICENSE_KEY = 'gpl'
