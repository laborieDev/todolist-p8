<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** php bin/console todolist:user:check [USERNAME] [PASSWORD] */
class CheckUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'todolist:user:check';

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
        ->setDescription('Check all users ...')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to check all user for roles')

        ->addArgument('username', InputArgument::REQUIRED, 'The username of the futur admin.')
        ->addArgument('password', InputArgument::REQUIRED, 'The password of the futur admin.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Get all users',
            '============',
        ]);


        $allUsers = $this->entityManager->getRepository(User::class)->findAll();

        $countUsers = count($allUsers);

        $output->writeln([
            'There are '.$countUsers.' users',
            '============',
        ]);

        foreach($allUsers as $user){
            if(in_array("ROLE_ADMIN", $user->getRoles())){
                $output->writeln([
                    'Admin exists !',
                    'Username : '.$user->getUsername(),
                    '============',
                ]);

                return 1;
            }
        }

        $adminUsername = $input->getArgument('username');
        $adminEmail = "admin@todolist.fr";
        $adminPassword = $input->getArgument('password');

        $adminUser = new User();
        $adminUser->setUsername($adminUsername);
        $adminUser->setEmail($adminEmail);
        $passwordEncoded = $this->encoder->encodePassword($adminUser, $adminPassword);
        $adminUser->setPassword($passwordEncoded);
        $adminUser->setRoles([
            User::USER_ADMIN_ROLE
        ]);

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $output->writeln([
            'Admin is created !',
            'Username : '.$adminUser->getUsername(),
            '============',
        ]);

        return 0;
    }
}
