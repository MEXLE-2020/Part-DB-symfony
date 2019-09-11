<?php
/**
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/.
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Services;

use App\Helpers\TreeViewNode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This Service generates the tree structure for the tools.
 * @package App\Services
 */
class ToolsTreeBuilder
{

    protected $translator;
    protected $urlGenerator;
    protected $keyGenerator;
    protected $cache;

    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator,
                                TagAwareCacheInterface $treeCache, UserCacheKeyGenerator $keyGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;

        $this->cache = $treeCache;

        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Generates the tree for the tools menu.
     * The result is cached.
     * @return TreeViewNode The array containing all Nodes for the tools menu.
     */
    public function getTree() : array
    {
        $key = "tree_tools_" .  $this->keyGenerator->generateKey();

        return $this->cache->get($key, function (ItemInterface $item) {
            //Invalidate tree, whenever group or the user changes
            $item->tag(["tree_tools", "groups", $this->keyGenerator->generateKey()]);

            $tree = array();
            $tree[] = new TreeViewNode($this->translator->trans('tree.tools.edit'), null, $this->getEditNodes());
            $tree[] = new TreeViewNode($this->translator->trans('tree.tools.show'), null, $this->getShowNodes());
            $tree[] = new TreeViewNode($this->translator->trans('tree.tools.system'), null, $this->getSystemNodes());
            return $tree;
        });
    }

    /**
     * This functions creates a tree entries for the "edit" node of the tool's tree
     * @return TreeViewNode[]
     */
    protected function getEditNodes() : array
    {
        $nodes = array();
        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.attachment_types'),
            $this->urlGenerator->generate('attachment_type_new'));
        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.categories'),
            $this->urlGenerator->generate('category_new'));
        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.devices'),
            $this->urlGenerator->generate('device_new'));
        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.suppliers'),
            $this->urlGenerator->generate('supplier_new'));
        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.manufacturer'),
            $this->urlGenerator->generate('manufacturer_new'));

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.storelocation'),
            $this->urlGenerator->generate('store_location_new'));

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.footprint'),
            $this->urlGenerator->generate('footprint_new'));

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.currency'),
            $this->urlGenerator->generate('currency_new'));

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.measurement_unit'),
            $this->urlGenerator->generate('measurement_unit_new'));

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.edit.part'),
            $this->urlGenerator->generate('part_new'));

        return $nodes;
    }

    /**
     * This function creates the tree entries for the "show" node of the tools tree
     * @return TreeViewNode[]
     */
    protected function getShowNodes() : array
    {
        $show_nodes = array();
        $show_nodes[] = new TreeViewNode($this->translator->trans('tree.tools.show.all_parts'),
            $this->urlGenerator->generate('parts_show_all')
        );

        return $show_nodes;
    }

    /**
     * This function creates the tree entries for the "system" node of the tools tree.
     * @return array
     */
    protected function getSystemNodes() : array
    {
        $nodes = array();

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.system.users'),
            $this->urlGenerator->generate("user_new")
        );

        $nodes[] = new TreeViewNode($this->translator->trans('tree.tools.system.groups'),
            $this->urlGenerator->generate('group_new')
        );

        return $nodes;
    }
}
