<?php

/**
 * Copyright 2017 SURFnet B.V.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace OpenConext\Profile\Value;

use OpenConext\Profile\Exception\InvalidArpDataException;
use Surfnet\SamlBundle\Exception\UnknownUrnException;
use Surfnet\SamlBundle\SAML2\Attribute\Attribute;
use Surfnet\SamlBundle\SAML2\Attribute\AttributeDefinition;
use Surfnet\SamlBundle\SAML2\Attribute\AttributeDictionary;

/**
 * The Arp value object represents the Arp configuration for a given entity.
 */
final class Arp
{
    /**
     * @var array The arp configuration is grouped on source. The source values are a collection of Attribute
     */
    private $arp;

    public static function createWith(array $arp, AttributeDictionary $dictionary = null)
    {
        // Input validation
        foreach ($arp as $attributeInformation) {
            if (!is_array($attributeInformation)) {
                throw new InvalidArpDataException('The attribute information in the arp should be an array.');
            }
            if (!self::isValidAttribute($attributeInformation)) {
                throw new InvalidArpDataException('The attribute information is formatted invalidly.');
            }
        }

        return new self($arp, $dictionary);
    }

    private function __construct(array $arp, AttributeDictionary $dictionary = null)
    {
        $arpCollection = [];

        // Create Attribute instances for all attributes
        foreach ($arp as $attributeName => $attributeDefinitionInformation) {
            $attributeSource = 'idp';
            $attributeDefinition = new AttributeDefinition($attributeName, $attributeName, $attributeName);

            // When the dictionary is available. Lookup the attribute in dictionary to load friendly attribute names
            if (!is_null($dictionary)) {
                try {
                    $attributeDefinition = $dictionary->getAttributeDefinitionByUrn($attributeName);
                } catch (UnknownUrnException $exception) {
                    // Use the previously created attributeDefinition.
                }
            }

            if (isset($attributeDefinitionInformation[0]['source'])) {
                $attributeSource = $attributeDefinitionInformation[0]['source'];
            }

            // The arp is grouped on attribute source
            $arpCollection[$attributeSource][] = new Attribute($attributeDefinition, $attributeDefinitionInformation);
        }

        $this->arp = $arpCollection;
    }

    /**
     * Tests the structure of the Arp attribute information.
     *
     * This information should be an array, should have the attribute names as its keys and have array values.
     * These array values can have two keys (value & source) the values aof these entries should be string.
     *
     * Example of a valid attribute information array:
     *
     * [
     *   'urn.mace.email' => [
     *     ['value' => '*'],
     *   ],
     *   'urn.mace.eduPersonTargetedId' => [
     *     [
     *       'value' => '*',
     *       'source' => 'sab',
     *     ]
     *   ],
     *   'urn.mace.orcid' => [
     *     [
     *       'value' => 'orcid.org/\d{4}-\d{4}',
     *       'source' => 'orcid',
     *     ],
     *     [
     *       'value' => 'sandbox.orcid.org/\d{4}-\d{4}',
     *       'source' => 'orcid',
     *     ],
     *   ],
     * ]
     *
     * @param array $attributeInformation
     * @return bool
     */
    private static function isValidAttribute(array $attributeInformation)
    {
        $validKeys = ['value', 'source'];

        foreach ($attributeInformation as $attributeInformationEntry) {
            foreach ($attributeInformationEntry as $key => $attributeConfigValue) {
                if (!in_array($key, $validKeys)) {
                    return false;
                }
                if (!is_string($attributeConfigValue)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function getNonIdpAttributes()
    {
        $attributes = $this->getAttributesGroupedBySource();
        unset($attributes['idp']);
        return $attributes;
    }

    public function getAttributesGroupedBySource()
    {
        return $this->arp;
    }
}
