<?php

class FeatureContext extends Behatch\Context\RestContext
{
    /**
     * @var \App\DataFixtures\UserFixtures
     */
    private $fixtures;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Coduo\PHPMatcher\Matcher
     */
    private $matcher;

    public function __construct(
        \Behatch\HttpCall\Request $request,
        \App\DataFixtures\UserFixtures $fixtures,
        \Doctrine\ORM\EntityManagerInterface $entityManager
    ) {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new \Coduo\PHPMatcher\Factory\SimpleFactory())->createMatcher();
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);

        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->entityManager);
        $fixturesExecutor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->entityManager, $purger);

        $fixturesExecutor->execute([$this->fixtures]);
    }

    /**
     * @Given /^the JSON matches expected template:$/
     */
    public function theJSONMatchesExpectedTemplate(\Behat\Gherkin\Node\PyStringNode $string)
    {
        $actual = $this->request->getContent();
        $this->assertTrue(
            $this->matcher->match($actual, $string->getRaw())
        );
    }
}
