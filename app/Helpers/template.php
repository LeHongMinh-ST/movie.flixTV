<?php
if (! defined('TI_VIEWS_DIR') ) {
    if (defined('APP_PATH')) {
        define('TI_VIEWS_DIR', APP_PATH.'Views/');
    } else {
        define('TI_VIEWS_DIR', 'app/Views');
    }
}

define('TI_MARKER_EXTEND_BLOCK_HERE', '{{{[[[{[{[{[INSERT_BASE_DATA_HERE]}]}]}]]]}}}');

$GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'] = array();

$GLOBALS['TI_CURRENT_BLOCKNAME'] = '';

$GLOBALS['TI_CURRENT_BASE_TEMPLATE'] = '';

function extend($filename) {
    $GLOBALS['TI_CURRENT_BASE_TEMPLATE'] = $filename;
}
function endExtend() {
    if (isset($GLOBALS['CI'])) {
        $GLOBALS['CI']->load->view($GLOBALS['TI_CURRENT_BASE_TEMPLATE']);
    }
    else {
        include realpath( TI_VIEWS_DIR . $GLOBALS['TI_CURRENT_BASE_TEMPLATE']);
    }
}

function yields($blockname){
    $GLOBALS['TI_CURRENT_BLOCKNAME'] = $blockname;

    ob_start();

    //end block
    $thisBlocksContent = ob_get_clean();

    if (array_key_exists($GLOBALS['TI_CURRENT_BLOCKNAME'], $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'])) {

        $thisBlocksContent = str_replace(
            TI_MARKER_EXTEND_BLOCK_HERE,
            $thisBlocksContent,
            $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'][$GLOBALS['TI_CURRENT_BLOCKNAME']]
        );
    }

    echo $thisBlocksContent;
}


function section($blockname) {
    $GLOBALS['TI_CURRENT_BLOCKNAME'] = $blockname;

    ob_start();
}


function endSection() {
    $thisBlocksContent = ob_get_clean();

    if (array_key_exists($GLOBALS['TI_CURRENT_BLOCKNAME'], $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'])) {

        $thisBlocksContent = str_replace(
            TI_MARKER_EXTEND_BLOCK_HERE,
            $thisBlocksContent,
            $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'][$GLOBALS['TI_CURRENT_BLOCKNAME']]
        );
    }
    $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'][$GLOBALS['TI_CURRENT_BLOCKNAME']] = $thisBlocksContent;
}

function getExtendedBlock() {
    echo TI_MARKER_EXTEND_BLOCK_HERE;
}

function blockRenderingNeccessary() {

    if (!array_key_exists($GLOBALS['TI_CURRENT_BLOCKNAME'], $GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'] )) {
        return true;
    }

    if (false === strpos($GLOBALS['TI_EXTENDED_BASE_TEMPLATE_DATA'][$GLOBALS['TI_CURRENT_BLOCKNAME']], TI_MARKER_EXTEND_BLOCK_HERE)) {
        return false;
    } else {
        return true;
    }
}