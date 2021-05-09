<?php
namespace App\Command;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** php bin/console todolist:task:check-anonymous */
class AnonymousTaskCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'todolist:task:check-anonymous';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->setDescription("Get all anonymous task ...")

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp("This command allows you to get all anonymous task and add the 'anonymous' user");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Get all anonymous tasks',
            '============',
            '',
        ]);

        $anonymousUsername = User::ANONYMOUS_USERNAME;
        $anonymousEmail = User::ANONYMOUS_EMAIL;
        $anonymousPassword = User::ANONYMOUS_PASSWORD;

        $anonymousUser = $this->entityManager->getRepository(User::class)->findBy([
            "username" => $anonymousUsername
        ]);

        if($anonymousUser == null){
            $anonymousUser = new User();
            $anonymousUser->setUsername($anonymousUsername);
            $anonymousUser->setEmail($anonymousEmail);
            $passwordEncoded = $this->encoder->encodePassword($anonymousUser, $anonymousPassword);
            $anonymousUser->setPassword($passwordEncoded);

            $this->entityManager->persist($anonymousUser);
        }

        $allAnonymousTask = $this->entityManager->getRepository(Task::class)->findBy([
            "user" => null
        ]);

        $countUsers = count($allAnonymousTask);

        $output->writeln([
            '',
            'There are '.$countUsers.' anonymous users',
            '============',
            '',
        ]);

        foreach($allAnonymousTask as $task){
            $task->setUser($anonymousUser);
            $this->entityManager->persist($task);
        }

        $this->entityManager->flush();

        $output->writeln([
            '',
            'All users have been saved',
            '============',
            '',
        ]);

        return 1;
    }
}
