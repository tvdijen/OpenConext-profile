<?php

/**
 * Copyright 2015 SURFnet B.V.
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

namespace OpenConext\Profile\Tests\Value;

use OpenConext\Profile\Value\ContactPerson;
use OpenConext\Profile\Value\ContactPersonList;
use OpenConext\Profile\Value\ContactType;
use OpenConext\Profile\Value\EmailAddress;
use PHPUnit_Framework_TestCase as TestCase;

class ContactPersonListTest extends TestCase
{
    /**
     * @test
     * @group ContactPerson
     */
    public function contact_person_list_can_be_filtered_according_to_predicate()
    {
        $filterCriterium = new ContactType(ContactType::TYPE_SUPPORT);

        $contactPersonList = new ContactPersonList([
            new ContactPerson(new ContactType(ContactType::TYPE_SUPPORT), new EmailAddress('first@invalid.email')),
            new ContactPerson(new ContactType(ContactType::TYPE_ADMINISTRATIVE)),
            new ContactPerson(new ContactType(ContactType::TYPE_SUPPORT), new EmailAddress('second@invalid.email')),
        ]);

        $filterPredicate = function (ContactPerson $contactPerson) use ($filterCriterium) {
            return $contactPerson->hasContactTypeOf($filterCriterium);
        };

        $expectedFilteredList = new ContactPersonList([
            new ContactPerson(new ContactType(ContactType::TYPE_SUPPORT), new EmailAddress('first@invalid.email')),
            new ContactPerson(new ContactType(ContactType::TYPE_SUPPORT), new EmailAddress('second@invalid.email'))
        ]);

        $actualFilteredList = $contactPersonList->filter($filterPredicate);

        $this->assertEquals($expectedFilteredList, $actualFilteredList);
    }

    /**
     * @test
     * @group ContactPerson
     */
    public function first_contact_person_can_be_retrieved_from_list()
    {
        $contactPersonList = new ContactPersonList([
            new ContactPerson(new ContactType(ContactType::TYPE_SUPPORT), new EmailAddress('first@invalid.email')),
            new ContactPerson(new ContactType(ContactType::TYPE_OTHER), new EmailAddress('second@invalid.email')),
        ]);

        $expectedFirstContactPerson = new ContactPerson(
            new ContactType(ContactType::TYPE_SUPPORT),
            new EmailAddress('first@invalid.email')
        );

        $actualFirstContactPerson = $contactPersonList->getFirstContactPerson();

        $this->assertEquals($expectedFirstContactPerson, $actualFirstContactPerson);
    }
}
