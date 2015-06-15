<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Layoutarea;

/**
 * Layoutarea manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LayoutareaManager
{
    /**
     * Return all Teasers for the given EID
     *
     * @param string $elementTypeID
     *
     * @return array
     */
    public function getFor($elementTypeID)
    {
        $elementTypeManager = Makeweb_Elementtypes_Elementtype_Manager::getInstance();

        $layoutElementTypes = $elementTypeManager->getByType(Makeweb_Elementtypes_Elementtype_Version::TYPE_LAYOUTAREA);

        $layoutAreas = array();

        foreach ($layoutElementTypes as $layoutElementTypeID => $layoutElementType) {
            $layoutElementTypeVersion = $layoutElementType->getVersion();
            $viabilityIDs = $layoutElementTypeVersion->getViabilityIDs();

            if (!in_array($elementTypeID, $viabilityIDs)) {
                continue;
            }

            //            $layoutAreas[$layoutElementTypeID] = new Makeweb_Teasers_Layoutarea($layoutElementTypeVersion);
            $layoutAreas[$layoutElementTypeID] = $layoutElementType->getLatest();
        }

        return $layoutAreas;
    }

}

