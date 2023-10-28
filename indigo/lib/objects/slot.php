<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_slot.
 */
class idg_slot_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [];
        $this->set_property_mandatory('name');
    }
}

/**
 * A slot is where the output produced by a renderer has its place inside the view.
 */
class idg_slot extends idg_view_element {

    function __construct(idg_treenode $parent = null) {
        parent::__construct($parent);
        $this->set_element_name('slot');
    }

    function _render(idg_document $document, idg_view $view): void {
        $idg_id = $this->get_idg_id();
        $name = $this->get_property('name');
        $renderers = $document->get_renderers($name);

        if (($filter = $this->get_property('filter')))
            $view->add_filter($filter);

        $has_anchors = false;
        foreach ($renderers as $renderer)
            if (($anchor = $renderer->get_property('anchor')))
                $has_anchors = true;

        if ($has_anchors)
            $view->stream_append('html-body', "<div class=\"$idg_id-anchor\">\n");

        foreach ($renderers as $renderer)
            $renderer->_render($document, $view);

        if ($has_anchors)
            $view->stream_append('html-body', "</div>\n");

        if ($filter)
            $view->enable_filter($filter, false);

        /** @todo implement this properly
          $style = $this->get_property('style');
          $style_link = $this->get_property('style-link');
          $style_link_hover = $this->get_property('style-link-hover');

          $list_style_image = $this->get_property('list-style-image');
          $list_style_position = $this->get_property('list-style-position');

          $css = '';

          if ($style)
          $css .= "	div.$idg_id { $style }\n";

          if ($style_link)
          $css .= "	div.$idg_id a { $style_link }\n";

          if ($style_link_hover)
          $css .= "	div.$idg_id a:hover { $style_link_hover }\n";

          if ($list_style_image || $list_style_position) {
          $css .= "	div.$idg_id ul {\n";

          if ($list_style_image)
          $css .= "		list-style-image: url($list_style_image);\n";

          if ($list_style_position)
          $css .= "	list-style-position: $list_style_position;";

          $css .= "	}\n\n";
          }

          /** @todo css-print?
          if ($anchor_count > 0)
          $css .= "	div.$idg_id-anchor {\n		display: none;\n	}\n\n";

          $view->stream_append('css', $css); */
    }
}
