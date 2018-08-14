<?php


namespace Subtext\Garbage\Test\Services;

use Subtext\Garbage\Services\Localization;
use Subtext\Garbage\Services\MessageFormatter;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversDefaultClass \Subtext\Garbage\Services\Localization
 */
class LocalizationTest extends TestCase
{
    /**
     * @var MessageFormatter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_formatter;

    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_translator;

    /**
     * @var YamlFileLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $_loader;

    /**
     * @var array An array of locales available for use
     */
    private static $_available = [];

    /**
     * @var array A dictionary of translations for testing
     */
    private static $_dictionary = [];

    /**
     * Test resource setup
     */
    public static function setUpBeforeClass()
    {
        $dictionary = [
            'english' => [
                'messages' => [
                    'numbers' => [
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three'
                    ],
                    'colors' => [
                        'red' => 'Red',
                        'blue' => 'Blue',
                        'green' => 'Green'
                    ],
                    'alerts' => [
                        'login' => 'The user: %s has been logged in',
                        'update' => 'Your name has been updated to %s',
                        'cart-items' => '{0}There are no items in %1$s cart.|{1} There is one item in %1$s cart.|[2,Inf[ There are %2$d items in %1$s cart.'
                    ]
                ],
                'jargon' => [
                    'items' => [
                        'product' => 'Product',
                        'user' => 'User',
                        'purchase' => 'Purchase',
                        'invoice' => 'Invoice'
                    ]
                ]
            ],
            'spanish' => [
                'messages' => [
                    'numbers' => [
                        'one' => 'Uno',
                        'two' => 'Dos',
                        'three' => 'Tres'
                    ],
                    'colors' => [
                        'red' => 'Rojo',
                        'blue' => 'Azul',
                        'green' => 'Verde'
                    ],
                    'alerts' => [
                        'login' => 'El usario: %s ha sido conectado',
                        'update' => 'Su nombre ha sido actualizado a %s',
                        'cart-items' => '{0} No hay artículos en el carrito de %1$s.|{1} Hay un artículo en el carrito de %1$s.|[2,Inf[ Hay %2$d artículos en el carrito de %1$s.'
                    ]
                ],
                'jargon' => [
                    'items' => [
                        'product' => 'Producto',
                        'user' => 'Usario',
                        'purchase' => 'Compra',
                        'invoice' => 'Factura'
                    ]
                ]
            ],
            'french' => [
                'messages' => [
                    'numbers' => [
                        'one' => 'Un',
                        'two' => 'Deux',
                        'three' => 'Trois'
                    ],
                    'colors' => [
                        'red' => 'Rouge',
                        'blue' => 'Bleu',
                        'green' => 'Vert'
                    ],
                    'alerts' => [
                        'login' => 'L\'utilisateur: %s a été connecté',
                        'update' => 'Votre nom a été mis à jour pour %s',
                        'cart-items' => '{0} Il n\'y a aucun article dans le panier de %1$s.|{1} Il y a un article dans le panier de %1$s.|[2,Inf[ Il y a %2$d articles dans le panier de %1$s.'
                    ]
                ],
                'jargon' => [
                    'items' => [
                        'product' => 'Produit',
                        'user' => 'Utilisateur',
                        'purchase' => 'Achat',
                        'invoice' => 'Facture d\'achat'
                    ]
                ]
            ]
        ];
        $enYml      = Yaml::dump($dictionary['english']['messages']);
        $enJson     = \json_encode($dictionary['english']['messages']);
        $enPhp      = var_export($dictionary['english']['messages'], true);
        $enDomain   = Yaml::dump($dictionary['english']['jargon']);
        $esYml      = Yaml::dump($dictionary['spanish']['messages']);
        $esJson     = \json_encode($dictionary['spanish']['messages']);
        $esPhp      = var_export($dictionary['spanish']['messages'], true);
        $esDomain   = Yaml::dump($dictionary['spanish']['jargon']);
        $frYml      = Yaml::dump($dictionary['french']['messages']);
        $frJson     = \json_encode($dictionary['french']['messages']);
        $frPhp      = var_export($dictionary['french']['messages'], true);
        $frDomain   = Yaml::dump($dictionary['french']['jargon']);
        $structure  = [
            'www' => [
                'sites' => [
                    'site-a' => [
                        'messages.en_US.yml' => $enYml,
                        'messages.en_US.json' => $enJson,
                        'messages.en_US.php' => "<?php\nreturn " . $enPhp . ";",
                        'jargon.en_US.yml' => $enDomain
                    ],
                    'site-b' => [
                        'messages.es_MX.yml' => $esYml,
                        'messages.es_MX.json' => $esJson,
                        'messages.es_MX.php' => "<?php\nreturn " . $esPhp . ";",
                        'jargon.es_MX.yml' => $esDomain
                    ],
                    'site-c' => [
                        'messages.fr_CA.yml' => $frYml,
                        'messages.fr_CA.json' => $frJson,
                        'messages.fr_CA.php' => "<?php\nreturn " . $frPhp . ";",
                        'jargon.fr_CA.yml' => $frDomain
                    ]
                ]
            ]
        ];
        vfsStream::setup('root', null, $structure);
        static::$_available  = ['en_US', 'en', 'es_MX', 'es', 'fr_CA', 'fr'];
        static::$_dictionary = $dictionary;
    }

    /**
     * Get mock dependencies before each test
     */
    public function setUp()
    {
        $this->_formatter  = $this->getMockBuilder(MessageFormatter::class)
                                  ->setMethods(null)
                                  ->getMock();
        $this->_translator = $this->getMockBuilder(Translator::class)
                                  ->setMethods(null)
                                  ->setConstructorArgs(['en_US', $this->_formatter])
                                  ->getMock();
        $this->_loader     = $this->getMockBuilder(YamlFileLoader::class)
                                  ->setMethods(null)
                                  ->getMock();
    }

    public function testCanSetDefaultsFromGlobals()
    {
        $this->assertEquals('en_US', $this->_translator->getLocale());
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'es,es-MX;q=0.8';
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-b'),
            'yml'
        );
        $service->configureFromEnvironment();
        $this->assertEquals('es_MX', $service->getLocale());
        $this->assertEquals('es_MX', $this->_translator->getLocale());
    }

    public function testCanSetDefaultsFromEnvironment()
    {
        \putenv('HTTP_ACCEPT_LANGUAGE=es-MX');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-b'),
            'yml'
        );
        $service->configureFromEnvironment();
        $this->assertEquals('es_MX', $service->getLocale());
        $this->assertEquals('es_MX', $this->_translator->getLocale());

        \putenv('HTTP_ACCEPT_LANGUAGE=fr-CA');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-c'),
            'yml'
        );
        $service->configureFromEnvironment();
        $this->assertEquals('fr_CA', $service->getLocale());
        $this->assertEquals('fr_CA', $this->_translator->getLocale());
    }

    public function testCanFallBackToDefaultLocale()
    {
        \putenv('HTTP_ACCEPT_LANGUAGE=de,de-de;q=0.8,de-lu;0.7,de-li-lu;q=0.5');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-a'),
            'yml'
        );
        $service->configureFromEnvironment();
        $this->assertEquals('en_US', $service->getLocale());
        $this->assertEquals('en_US', $this->_translator->getLocale());
    }

    public function testCanSetDefaultLocaleManually()
    {
        \putenv('HTTP_ACCEPT_LANGUAGE=es-MX');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-b'),
            'yml'
        );
        $service->configureFromEnvironment();
        $this->assertEquals('es_MX', $service->getLocale());
        $this->assertEquals('es_MX', $this->_translator->getLocale());
        $service->setLocale('en_US');
        $this->assertEquals('en_US', $service->getLocale());
        $this->assertEquals('en_US', $this->_translator->getLocale());
    }

    public function testCanUseTranslatorTranslate()
    {
        $dictionary = static::$_dictionary;
        \putenv('HTTP_ACCEPT_LANGUAGE=en-US');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-a'),
            'yml'
        );
        $service->configureFromEnvironment();
        $expected = $dictionary['english']['messages']['numbers']['two'];
        $this->assertEquals($expected, $service->translate('numbers.two'));

        $service->setLocale('es_MX');
        $service->setDefaults(
            static::$_available,
            vfsStream::url('root/www/sites/site-b'),
            'yml'
        );
        $expected = $dictionary['spanish']['messages']['colors']['red'];
        $this->assertEquals($expected, $service->translate('colors.red'));
    }

    public function testCanLazilyLoadTranslationDomain()
    {
        $dictionary = static::$_dictionary;
        \putenv('HTTP_ACCEPT_LANGUAGE=fr');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            vfsStream::url('root/www/sites/site-c'),
            'yml'
        );
        $service->configureFromEnvironment();
        $expected = $dictionary['french']['jargon']['items']['invoice'];
        $actual = $service->translate('items.invoice', [], 'jargon');
        $this->assertEquals('fr_CA', $service->getLocale());
    }

    public function testCanUseTranslatorPlural()
    {
        $dictionary = static::$_dictionary;
        $path = vfsStream::url('root/www/sites/site-c');
        $service = new Localization(
            $this->_translator,
            $this->_loader,
            static::$_available,
            $path,
            'yml'
        );
        $service->setLocale('fr_CA');
        $service->setDefaults(static::$_available, $path, 'yml');
        $key = $dictionary['french']['messages']['alerts']['cart-items'];
        $start = strrpos($key, '[');
        $format = trim(substr($key, $start + 1));
        $expected = sprintf($format, 'Bill', 2);
        $actual = $service->plural('alerts.cart-items', 2, ['Bill']);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Reset any headers set from the previous test
     */
    public function tearDown()
    {
        \putenv('HTTP_ACCEPT_LANGUAGE');
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
    }
}