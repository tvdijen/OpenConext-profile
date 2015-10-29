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

namespace OpenConext\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LocaleController extends Controller
{
    public function switchLocaleAction(Request $request)
    {
        $returnUrl = $request->query->get('return-url');

        // Return URLs generated by us always include a path (ie. at least a forward slash)
        // @see https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/Request.php#L878
        $domain = $request->getSchemeAndHttpHost() . '/';

        if (strpos($returnUrl, $domain) !== 0) {
            $this->get('logger')->error(sprintf(
                'Illegal return-url for redirection after changing locale, aborting request'
            ));
            throw new BadRequestHttpException('Invalid return-url given');
        }

        $form = $this->createForm(
            'profile_switch_locale',
            null,
            [
                'current_locale' => $request->getLocale(),
                'return_url' => $returnUrl
            ]
        );
        $form->handleRequest($request);

        if (!$form->isValid()) {
            throw new BadRequestHttpException('Form is invalid');
        }

        if ($form->get('locale_en')->isClicked()) {
            $locale = 'en';
        } elseif ($form->get('locale_nl')->isClicked()) {
            $locale = 'nl';
        } else {
            throw new BadRequestHttpException('No locale selected');
        }

        $allowedLocales = $this->container->getParameter('locales');

        if (!in_array($locale, $allowedLocales)) {
            throw new BadRequestHttpException(sprintf('Locale "%s" is not supported', $locale));
        }

        $this->get('session')->set('_locale', $locale);

        return $this->redirect($returnUrl);
    }
}