<?php

namespace Dte\BtsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FixturesWebTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Console\Application
     */
    protected $application;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update --force');
        $this->runCommand('doctrine:fixtures:load -n');
    }

    /**
     * Login by username
     * @param  string $username
     */
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
     * Run symfony command
     *
     * @param  string $command
     * @return int
     */
    protected function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * Get Console Application
     * @return  \Symfony\Bundle\FrameworkBundle\Console\Application
     */
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
