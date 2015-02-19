<?php

namespace Dte\BtsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FixturesWebTestCase extends WebTestCase
{
    protected $application;

    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update --force');
        $this->runCommand('doctrine:fixtures:load -n');
    }

    public function logInByUsername($username)
    {

        $this->client->getCookieJar()->set(new Cookie(session_name(), true));
        $this->client->request('GET', '/');

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('DteBtsBundle:User')->loadUserByUsername($username);

        if ($user) {
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'secured_area', $user->getRoles());
            self::$kernel->getContainer()->get('security.context')->setToken($token);

            $session = $this->client->getContainer()->get('session');
            $session->set('_security_' . 'secured_area', serialize($token));
            $session->save();
        }
    }

    public function tearDown()
    {
        $this->runCommand('doctrine:database:drop --force');
        $this->client = null;
    }

    /**
     * run symfony command
     * @param  string $command
     */
    protected function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected function getApplication()
    {
        if (null === $this->application) {
            $this->client = static::createClient();

            $this->application = new Application($this->client->getKernel());
            $this->application->setAutoExit(false);
        }

        return $this->application;
    }
}
