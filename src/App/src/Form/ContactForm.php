<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App\Form;

use Zend\Form\Form;

/**
 * Class ContactForm
 * @package Frontend\App\Form
 */
class ContactForm extends Form
{
    /**
     * ContactForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'contactForm', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'type' => 'UserMessageFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'contact_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                    'message' => 'The form used to make the request has expired and was refreshed. Please try again.'
                ]
            ]
        ]);

        $this->add([
            'name' => 'captcha',
            'type' => 'Captcha',
            'options' => [
                'label' => 'Please verify you are human',
                'captcha' => [
                    'class' => 'recaptcha',
                    'options' => [
                        'site_key' => '6LcskCMTAAAAAM14rWOGRy4mMTfwk41dJHF75B2A',
                        'secret_key' => '6LcskCMTAAAAANFDwPGBvXxPgVuDQFJxfXrMmEsX',
                        'theme' => 'light',
                    ]
                ],
            ]
        ], ['priority' => -100]);
    }
}
